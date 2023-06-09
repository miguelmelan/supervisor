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

    siblings_resolution_descriptions = sys.argv[1]
    
    prompt = f"""You have to extract a list of recommended actions from a text corresponding to resolution descriptions of alerts. Each resolution description is related to a different alert, they are separated by the following sequence '*****'. The answer should be a JSON object with results property valued by an array of strings corresponding to the list of recommended actions extracted from the text. These recommended actions must be in the form of imperative sentences. The list must be ordered so that most likely recommended actions first. If no recommended action is identified then the array should be empty.
Here is the text: "{siblings_resolution_descriptions}"."""

    answer = llm(prompt)
    answer = answer.replace('Answer:', '').strip()
    answer = json.loads(answer)

    print(json.dumps(answer))

if __name__ == "__main__":
    main()