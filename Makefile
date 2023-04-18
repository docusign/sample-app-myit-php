registry_name    				= mysmapp
version      					= latest
storage_account_name 			= myitstate
storage_account_container_name 	= myittfstate
react_app_api_base_url			= https://myit.dssigmadev.link/api
react_app_web_socket_url		= wss://myit.dssigmadev.link

.PHONY: help
help:
	@echo "make build-frontend         # build frontend docker image"
	@echo "make push-frontend          # push frontend docker image"
	@echo "make build-backend          # build backend docker image"
	@echo "make push-backend           # push backend docker image"
	@echo "make login         	   	   # get Azure login"
	@echo "make acr-login         	   # get Azure repo login"
	@echo "make deploy         		   # deploy the stack to the server"
	@echo "make build-all          	   # build fe and be images"
	@echo "make push-all          	   # push fe and be images"
	@echo "make full-scope             # buld && push && deploy"
	@echo "make request-certs		   # Run request SSL certificate"
	@echo "make renew-certs			   # Renew Let's Encrypt certificate"
	@echo "make help          		   # Show this help"

.PHONY: build-frontend
build-frontend:
	docker build -t my-it-frontend:latest --build-arg REACT_APP_WEB_SOCKET_URL=$(react_app_web_socket_url) --build-arg REACT_APP_API_BASE_URL=$(react_app_api_base_url) -f frontend/frontend.Dockerfile ./frontend
	docker tag my-it-frontend:latest $(registry_name).azurecr.io/my-it-frontend:$(version)

.PHONY: push-frontend
push-frontend:
	$(MAKE) acr-login
	docker push $(registry_name).azurecr.io/my-it-frontend:$(version)

.PHONY: build-backend
build-backend:
	az storage blob download \
		--account-name $(storage_account_name) \
		--container-name $(storage_account_container_name) \
		--name sec.env \
		--file ./backend/sec.env
	az storage blob download \
		--account-name $(storage_account_name) \
		--container-name $(storage_account_container_name) \
		--name docusign_private.key \
		--file ./backend/storage/docusign_private.key
	sed '/# DocuSign auth parameters/,+11 d' < backend/.env.example > backend/.env.temp
	cat backend/.env.temp backend/sec.env > backend/.env && rm backend/.env.temp
	docker build -t my-it-backend-php:latest -f backend/docker/php/php.Dockerfile ./backend
	docker build -t my-it-backend-nginx:latest -f backend/docker/nginx/nginx.Dockerfile backend/docker/nginx/
	docker build -t my-it-backend-node:latest -f backend/docker/node/node.Dockerfile ./backend
	docker tag my-it-backend-php:latest $(registry_name).azurecr.io/my-it-backend-php:$(version)
	docker tag my-it-backend-nginx:latest $(registry_name).azurecr.io/my-it-backend-nginx:$(version)
	docker tag my-it-backend-node:latest  $(registry_name).azurecr.io/my-it-backend-node:$(version)

.PHONY: push-backend
push-backend:
	$(MAKE) acr-login
	docker push $(registry_name).azurecr.io/my-it-backend-php:$(version)
	docker push $(registry_name).azurecr.io/my-it-backend-nginx:$(version)
	docker push $(registry_name).azurecr.io/my-it-backend-node:$(version)

.PHONY: login
login:
	az login

.PHONY: acr-login
acr-login:
	az acr login --name $(registry_name)

.PHONY: deploy
deploy:
	ANSIBLE_CONFIG=~/infra/deploy/ansible.cfg ansible-playbook -i infra/deploy/hosts.yaml ./infra/deploy/deploy.yml

.PHONY: request-certs
request-certs:
	ANSIBLE_CONFIG=~/infra/deploy/ansible.cfg ansible-playbook -i infra/deploy/hosts.yaml ./infra/deploy/get-cert.yml

.PHONY: renew-certs
renew-certs:
	ANSIBLE_CONFIG=~/infra/deploy/ansible.cfg ansible-playbook -i infra/deploy/hosts.yaml ./infra/deploy/cerbot-renew.yml

.PHONY: build-all
build-all:
	$(MAKE) build-frontend
	$(MAKE) build-backend

.PHONY: push-all
push-all:
	$(MAKE) push-frontend
	$(MAKE) push-backend

.PHONY: full-scope
full-scope:
	$(MAKE) build-frontend
	$(MAKE) build-backend
	$(MAKE) login
	$(MAKE) push-frontend
	$(MAKE) push-backend
	$(MAKE) deploy
