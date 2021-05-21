## DEMO APIV3 OVERVIEW #

This repository is intended to help ISVs to integrate our API in their products. 

It contains examples of sequences of API calls to implement a standard document signature workflow.

As of today, examples are provided in the following programming languages:

* PHP


### Getting started ###

Clone the repository

Checkout the branch corresponding to the desired progamming language

* PHP language -> git checkout php

alternatively, you can clone only the needed branch

* git clone --branch `<branch-name>` `<remote-repo-url>`

### Docker ###

The project contains a minimal dockerfile with a php7+apache base image so that you can launch the demo behind an Apache web server in a container.

If you want to run the demo in a container, Docker Desktop must be installed on your machine

### Demo application usage ###

Assign your licence token and contract definition id to the corresponding variables defined at the top of the index file.

build the docker image

Launch the container

When you point your browser to the demo web page, you can see 4 buttons

* Create contract : creates one contract with 1 document and 2 recipients
* Autoclose : toggles the auto-validation (automatic countersignature) mode of the contract to 'on'
* Get status : retreives the contrat status (OPEN, SINED, ARCHIVED, ABANDONNED)
* Get signed contrat : downloads and display the signed contrat

Autoclose and Get Status should be cliked after the contract creation.

Get signed contract should be clicked after contract signature and validation (validation is not necessary if autoclose was cliked before the signature).

Otherwise, you wil get error messages.

### Other

The 'doc-other -methods' file contains examples of extra api calls. The API calls contained in that file are not actually implemented in the demo app.
