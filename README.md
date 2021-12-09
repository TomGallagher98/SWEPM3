# Moodle Integration
## Prerequisites
* [Docker](https://www.docker.com/)
* [Docker Compose v2](https://docs.docker.com/compose/cli-command/)
## Service Setup
In order to get all necessary services running, use the provided docker-compose files:

* docker-compose-persistence.yml
* docker-compose-api.yml

The **persistence** composition file includes containers for a postgres instance (database) and an optional
admin UI for the database (admin) as well as an elasticsearch instance (elastic). The used images are publicly
available at hub.docker.com, and should be downloaded automatically when running the compose file.

The **api** composition file includes the custom services providing the necessary APIs you have to use, including
the Data Distribution Service (dds) and the Search Service (search). You can find the Docker images in the
**images** folder. In order to use them, use the following commands:


```
docker load -i ./images/dds.tar
docker load -i ./images/search.tar
```

Both the postgres and the Elasticsearch containers are mounted to a volume on the local machine. If you are
working on Linux/macOS both volumes should be created inside **.volume** wherever your .yml files are
located. On Windows, a named volume is needed for the postgres container. This means if you want to
remove all data you have to remove both the **volume** folder as well as the named volume via the Docker CLI.

To get them all up and running, continue with the following steps:

1. Start all **persistence** containers:

```
docker compose -f ./service-composition/docker-compose-persistence.yml up
```

2. Before starting the other containers, you have to add the uuid postgres extension by executing the
following SQL command on the database:


```
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";
```
An easy way to do this is to use the admin UI running on port 2080 with the following credentials:

```
System: PostgreSQL
Server: database (name of the container)
Username: root (according to env variable)
Password: postgres (according to env variable)
Database: dds_db
```

After a successful login you should be able to select **SQL command** on the left side, which guides you to a
small editor allowing you to execute SQL commands. You can also execute the command directly
inside the container (e.g. with docker exec) or use any other database administration tool.

3. Make sure Elasticsearch is up and running. You can use the curl command below to send a REST
request directly to the Elasticsearch instance. If the response has the status code 200 and the status
attribute in the JSON object is green or yellow, you should be ready to continue.
```
curl --request GET \
    --url 'http://localhost:9200/_cluster/health?pretty=true'
```

4. Start the remaining **api** containers:

```
docker compose -f ./service-composition/docker-compose-api.yml up
```
The Data Distribution Service should be able to create all necessary tables inside the postgres database
**dds_db**. The Search service should create an index called **learning-objects-dev**. You can check the
postgres database again via the admin UI and use the following curl command to see if the index has been
created:

```
curl --request GET \
    --url http://localhost:9200/learning-objects-dev/_search
```

5. Add the resource type with the name **LEARNING_OBJECT** in **resource_types** table inside the **dds_db**.


6. Add a provider to the table **providers** inside the **dds_db** (e.g.: name=Moodle). After adding a new
provider the dds has to be restarted. You can either stop and restart the whole composition or just the
dds.


7. Register the search service as consumer by using the **/consumer** endpoint

```
curl --request POST \
    --url http://localhost:5003/consumers \
    --header 'Content-Type: application/json' \
    --data '{
        "name": "Search Service",
        "endpoint": "http://search:5002/resources",
        "resources": [
            {
                "name": "LEARNING_OBJECT"
            }
        ]
}'
```

Now you should be able to test the whole flow by sending a resource (learning object) to the Data
Distribution Service, which should push all incoming learning objects to all registered consumers interested in
resources of the type **LEARNING_OBJECT**.

### Send Learning Objects to the Data Distribution Service
Learning Objects can be sent according to the following curl command (make sure to replace the provider ID
in the query parameter). The ID of a learning object has to be provided by you/moodle and
can be any string (Doesn't have to be a UUID). Description, syllabi, tags and groups are optional. This
metadata can be used to get specific search result. You have to provide them whenever you create a new
learning activity, in order to pass them to to the endpoint.

```
curl --request PUT \
  --url 'http://localhost:5003/learning-objects?provider_id=<PROVIDER_ID>' \
  --header 'Content-Type: application/json' \
  --data '[
    {
      "id": "d848cc93-cea8-469e-9a3d-e197dea608da",
      "title": "Fractions",
      "name": "Fractions",
      "description": "This activity contains a bunch of simple exercises about fractions",
      "syllabi": [
        "0adb6179-337c-4783-86fd-262b4bbf0736"
      ],
      "tags": [
        "Maths",
        "1st grade"
      ],
      "groups": [
        "Maths A"
      ]
    }
]'
```

### Search for Learning Objects

After the learning object has been successfully pushed to the Search Service, you can use the **/search**
endpoint for searching learning objects. The given query is used to search for any matching documents. The
syllabi array can be empty.

```
curl --request POST \
    --url http://localhost:5002/search \
    --header 'Content-Type: application/json' \
    --data '{
        "query": "fractions",
        "syllabi": [
    ]
}'
```

## Moodle Setup

To get you started with Moodle, you can also use Docker to quickly create an instance. The compose file uses
publicly available images from [Bitnami](https://bitnami.com/stack/moodle/containers). Running this for the first time can take a couple of minutes. Moodle
should be reachable at [http://localhost:8080.](http://localhost:8080.)

```
docker compose -f ./moodle-composition/docker-compose.yml up
```
