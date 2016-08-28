This is a php aplication that managed databases. It shows the differences between two databases from the same host.

The problem:
A lot of web site have the configuration and the data content in the database. It is a problem when there are many developers and also the information from the life is changing.
When a developers change something on his dev and push to stage, another developer must sync his dev with stage in order to be able to add his own changes.
So it is important to be able to see the differences in the databases, to be able to select the table and the row that have to be changed.

Soltion:
This app get the differences between two databases: table is added/droped, fields from tables are droped/added, rows from a table are droped/added/changed.
Because it is a php script that gets the differences, we use javascript in order to not get a time limit error.
Also the app writes a sql file (diff/dev.sql) using exec command. sql file could contain create, after, insert, delete, update queries.

How it works:
* add the credentials for your mysql server and the databases
* click on get the diff(one will get a list with the tables with differences- tableName:typeOfDifference:indexName:numberOfDifferentRows)
* click on the table to see the differences
* if the column has more than 100 characters click on view to see the differences(it uses dynamic programing to see the differences)
* click on make sql all to get the changes in the sql file (warning: do not click more times on this button)


Todos:
* Verification for credentials and databases existences
* Verification for writing to sql file
* verification for write to write file with exec
* process the data before writting in the sql file for special characters
* Get relation between table and be able to merge the conflicts!!!!
* add ignore tables field
* get correct table index, write sql query using that index
