from langchain.agents import create_pandas_dataframe_agent
from langchain.llms import AzureOpenAI
from dotenv import load_dotenv
import pandas as pd
import json
import sys
import os

def load_robots_logs(csv, date_format, verbose):
    if verbose: print("==== loading robots logs")
    
    columns = ['Level', 'ProcessName', 'TimeStamp', 'Message', 'JobKey']

    if os.path.exists(csv):
        data = pd.read_csv(csv, usecols=columns)
        data['TimeStamp'] = pd.to_datetime(data['TimeStamp'], format=date_format)
    else:
        data = pd.DataFrame()
    
    if verbose:
        print(data.head())
        print("==== robots logs loaded")

    return data

def load_jobs(csv, date_format, verbose):
    if verbose: print("==== loading jobs")

    columns = ['StartTime', 'EndTime', 'State', 'CreationTime', 'Id']

    if os.path.exists(csv):
        data = pd.read_csv(csv, usecols=columns)
        data['StartTime'] = pd.to_datetime(data['StartTime'], format=date_format)
        data['EndTime'] = pd.to_datetime(data['EndTime'], format=date_format)
        data['CreationTime'] = pd.to_datetime(data['CreationTime'], format=date_format)
    else:
        data = pd.DataFrame()
    
    if verbose:
        print(data.head())
        print("==== jobs loaded")

    return data

def load_machines(csv, verbose):
    if verbose: print("==== loading machines")

    columns = ['SessionId', 'MachineId', 'MachineName', 'HostMachineName', 'RuntimeType', 'MachineType', 'Status', 'IsUnresponsive', 'Runtimes', 'UsedRuntimes']

    if os.path.exists(csv):
        data = pd.read_csv(csv, usecols=columns)
    else:
        data = pd.DataFrame()
    
    if verbose:
        print(data.head())
        print("==== machines loaded")

    return data

def load_queue_items(csv, date_format, verbose):
    if verbose: print("==== loading queue items")

    columns = ['QueueDefinitionId', 'Status', 'ReviewStatus', 'Reference', 'DueDate', 'RiskSlaDate', 'Priority', 'DeferDate', 'StartProcessing', 'EndProcessing', 'RetryNumber', 'CreationTime', 'Progress']

    if os.path.exists(csv):
        data = pd.read_csv(csv, usecols=columns)
        data['DueDate'] = pd.to_datetime(data['DueDate'], format=date_format)
        data['RiskSlaDate'] = pd.to_datetime(data['RiskSlaDate'], format=date_format)
        data['DeferDate'] = pd.to_datetime(data['DeferDate'], format=date_format)
        data['StartProcessing'] = pd.to_datetime(data['StartProcessing'], format=date_format)
        data['EndProcessing'] = pd.to_datetime(data['EndProcessing'], format=date_format)
        data['CreationTime'] = pd.to_datetime(data['CreationTime'], format=date_format)
    else:
        data = pd.DataFrame()
    
    if verbose:
        print(data.head())
        print("==== queue items loaded")

    return data

def classify_text(text, llm, verbose):
    if verbose: print("==== classifying text")

    prompt = f"""You have to analyze some UiPath Orchestrator entities in order to find uncommon behaviors.
You have access to these data sources:
- Jobs execution history
- Robots logs
- Machines details
- Queue items details
Please classify the following text by selecting the right data sources.
You need to know that the text is a condition for an alerting system to raise an alert when the condition is met. To verify the condition, we need information present in one or more of the data sources. The data source is right if it's needed to make the condition verified. Give me the answer as a JSON object with results property valued by an array of objects with 2 properties: 'data_source' as a string corresponding to the data source identified, 'related_text_parts' as an array of strings identifying the parts of the text related to the data source. If none of the data sources can help to classify all parts of the text, give an answer with -1 for data_source property and the parts of the text related to the inability to be classified for related_text_parts property. Keep in mind that the text can be splitted so that the links between data_source and related_text_parts are as precise as possible. If only one data source is identified the corresponding related_text_parts array should contain only one string with the whole text.
Here is the text: "{text}"."""
    
    answer = llm(prompt)
    answer = answer.replace('Answer:', '').strip()
    answer = json.loads(answer)

    if verbose:
        print(json.dumps(answer, indent=2))
        print("==== text classified")

    return answer

def verify_text(text, llm, classification_results, df_robots_logs, df_jobs, df_machines, df_queue_items, verbose):
    if verbose: print("==== verifying text")

    # Instantiate agents
    robots_logs_agent = create_pandas_dataframe_agent(llm, df_robots_logs, verbose=verbose)
    jobs_agent = create_pandas_dataframe_agent(llm, df_jobs, verbose=verbose)
    machines_agent = create_pandas_dataframe_agent(llm, df_machines, verbose=verbose)
    queue_items_agent = create_pandas_dataframe_agent(llm, df_queue_items, verbose=verbose)

    answers = list()
    for result in classification_results["results"]:
        data_source = result["data_source"]
        related_text_parts = result["related_text_parts"]
        if isinstance(related_text_parts, list):
            related_text_parts = ", ".join(related_text_parts)
            
        prompt = f"""The data frame contains {data_source} of a UiPath Orchestrator.
You must determine if the text related to the current data frame is completely verified. It is a list of strings, each one being a condition to verify.
You must give an answer as a JSON object (double quotes for properties and values) with 3 properties: "result" as a boolean telling if condition is met, "reason" as a string giving the reason explaining the result, "sources" (when the condition is met) as an array of unique strings storing the """

        if data_source == "Robots logs" or data_source == "Jobs execution history":
            prompt += "Job Id"
        elif data_source == "Machines details":
            prompt += "Machine Id"
        elif data_source == "Queue items details":
            prompt += "Queue Definition Id"
        
        prompt += f""". Here is the text related to the current data frame: "{related_text_parts}"."""

        if data_source == "Robots logs" and not df_robots_logs.empty:
            answer = robots_logs_agent.run(prompt)
        elif data_source == "Jobs execution history" and not df_jobs.empty:
            answer = jobs_agent.run(prompt)
        elif data_source == "Machines details" and not df_machines.empty:
            answer = machines_agent.run(prompt)
        elif data_source == "Queue items details" and not df_queue_items.empty:
            answer = queue_items_agent.run(prompt)
        elif data_source == -1:
            answer = '{"error": "Data source not identified"}'
        else:
            answer = '{"error": "There is no data"}'

        answers.append({
            "data_source": data_source,
            "original_text": text,
            "related_text_parts": result["related_text_parts"],
            "answer": json.loads(answer)
        })

    if verbose:
        print(json.dumps(answers, indent=2))
        print("==== text verified")

    return answers

def main():
    load_dotenv()
    verbose = False

    # Instantiate LLM
    llm = AzureOpenAI(
        temperature=0,
        deployment_name="text-davinci-003", 
        model_name="text-davinci-003",
        verbose=verbose
    )

    conditions = sys.argv[1]
    robots_logs_csv = sys.argv[2]
    jobs_csv = sys.argv[3]
    machines_csv = sys.argv[4]
    queue_items_csv = sys.argv[5]

    # Load CSV files
    date_format = '%Y-%m-%dT%H:%M:%S.%fZ'
    df_jobs = load_jobs(jobs_csv, date_format, verbose)
    df_robots_logs = load_robots_logs(robots_logs_csv, date_format, verbose)
    df_machines = load_machines(machines_csv, verbose)
    df_queue_items = load_queue_items(queue_items_csv, date_format, verbose)

    classification_results = classify_text(conditions, llm, verbose)
    verification_results = verify_text(
        conditions,
        llm,
        classification_results,
        df_robots_logs,
        df_jobs,
        df_machines,
        df_queue_items,
        verbose
    )
    results = {
        'classifications': classification_results,
        'verifications': verification_results,
    }

    print(json.dumps(results))

if __name__ == "__main__":
    main()