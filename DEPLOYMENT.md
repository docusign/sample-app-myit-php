# Introduction
MyIT Sample App

# License information
This repository uses the MIT License. See [LICENSE](https://github.com/docusign/sample-app-myit-php/blob/main/LICENSE) for more information.



## Deployment process

### Requirement

- Docker
- Docker Compose (modern Docker installation already has Docker Compose)
- Make
- Ansible
- SSH public key must be added to the VM (ask DevOps to add it)
- Access to the Azure subscription
- Add the sec.env and docusign_private.key to the Azure storage (add the storage account name and container to the make file)

1. Log in to Azure
```
make login
```
2. Build images 
```
make build-all
```
3. Push images
```
make push-all
```
4. Deploy the app
```
make deploy
```