Title: Syncing Two PostgreSQL Databases Faster
Date: 2016-7-17 19:10
Tags: postgresql, bash
Category: SQL

Imagine you run two database machines hosting structurally the same databases on two separate servers and you need to transfer data from one to another. Not very often, let's say once a month. Your tables aren't small nor huge, let's say millions rows in general.

You're going to use `pg_dump` and pipe it to `psql`, but the indices on your tables will slow you down a lot.

That's why you'll want to drop all indices and constraints (`drop_indices_constraints.sql`):

    :::sql
    SELECT
        'DROP INDEX ' || schemaname || '.' || indexname || ';'
    FROM pg_indexes
    WHERE schemaname IN (SELECT unnest(string_to_array(:'schemas', ',')))
    UNION ALL
    SELECT 'ALTER TABLE ' ||
        tc.table_schema ||
        '.' ||
        tc.table_name ||
        ' DROP CONSTRAINT ' ||
        tc.constraint_name  ||
        ';'
    FROM information_schema.table_constraints tc
    JOIN information_schema.constraint_column_usage ccu ON (tc.constraint_catalog = ccu.constraint_catalog AND tc.constraint_schema = ccu.constraint_schema AND tc.constraint_name = ccu.constraint_name)
    WHERE tc.table_schema IN (SELECT unnest(string_to_array(:'schemas', ',')));

Then you will transfer the data:

    :::bash
    pg_dump -a -t "schema1.*" -t "schema2.*" -O -d source -v | psql -h localhost -d target

And restore the already dropped indices and constraints (`create_indices_constraints.sql`):

    :::sql
    SELECT indexdef || ';'
    FROM pg_indexes
    WHERE schemaname IN (SELECT unnest(string_to_array(:'schemas', ',')))
    UNION ALL
    SELECT 'ALTER TABLE ' ||
        tc.table_schema ||
        '.' ||
        tc.table_name ||
        ' ADD CONSTRAINT ' ||
        tc.constraint_name ||
        ' ' ||
        tc.constraint_type ||
        '(' ||
        string_agg(ccu.column_name::text, ', ')|| -- column order should be taken into account here
        ');'
    FROM information_schema.table_constraints tc
    JOIN information_schema.constraint_column_usage ccu ON (tc.constraint_catalog = ccu.constraint_catalog AND tc.constraint_schema = ccu.constraint_schema AND tc.constraint_name = ccu.constraint_name)
    WHERE tc.table_schema IN (SELECT unnest(string_to_array(:'schemas', ',')))
        AND tc.constraint_type = 'PRIMARY KEY'
    GROUP BY
        tc.table_schema,
        tc.table_name,
        tc.constraint_name,
        tc.constraint_type;

## Few sidenotes

1. Run the second piece of code first. If you forget, run that code on the source database.
2. Notice the `:schemas`. Variable assignment is one of the `psql` features I really like.

The bash script proposal might look as follows:

    :::bash
    # store indices and constraint definitions
    psql -d target -v schemas='schema1','schema2' -i create_indices_constraints.sql > create.sql

    # drop indices and constraints
    psql -d target -v schemas='schema1','schema2' -i drop_indices_constraints.sql | psql -d target

    ​# load data
    pg_dump -a -t "schema1.*" -t "schema2.*" -O -d source -v | psql -h localhost -d target

    #renew indices and constraints
    psql -d target -i create.sql
    ​