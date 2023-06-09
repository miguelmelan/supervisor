import sys
from langchain.llms import AzureOpenAI
from dotenv import load_dotenv
import json

def main():
    load_dotenv()

    # Instantiate LLM
    llm = AzureOpenAI(
        temperature=0,
        deployment_name="text-davinci-003", 
        model_name="text-davinci-003",
        verbose=False
    )

    recurrence = sys.argv[1]
    
    prompt = f"""Give me the cron expressions related to the following text. The answer should be a JSON object with results property valued by an array of objects with 2 properties: 'cron' as a string corresponding to the cron expression, 'description' as a string corresponding to an explanation of the cron expression in natural language. If no cron expression is identified then the array should be empty.
Here is the text: "{recurrence}"."""

    answer = llm(prompt)
    answer = answer.replace('Answer:', '').strip()
    answer = json.loads(answer)

    print(json.dumps(answer))

if __name__ == "__main__":
    main()