FROM laravelsail/php81-composer

RUN apt-get update && apt-get install -y pip
RUN pip install langchain
RUN pip install python-dotenv
RUN pip install openai
RUN pip install pandas
RUN pip install tabulate