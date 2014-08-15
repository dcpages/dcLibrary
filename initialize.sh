#!/bin/bash

# Check for git
git --version > /dev/null 2>&1
GIT_INSTALLED=$?

[[ $GIT_INSTALLED -ne 0 ]] && { printf "!!! Git not installed!\n!!! App initialization aborted!\n"; exit 0; }

# Get input for Vagrantfile
qa_app_env=""
while [[ $qa_app_env == "" ]]; do
  read -p "Enter QA APP_ENV [project-name-qa]: " qa_app_env
done

qa_host=""
while [[ ! $qa_host =~ ^.*\..*+$ ]] || [[ $qa_host =~ \/\/ ]]; do
  if [[ $qa_host != "" ]]; then
    echo "Enter a valid host domain, no protocol"
  fi
  read -p "Enter QA Host [domain only]: " qa_host
done

dev_ip_block=""
while [[ ! $dev_ip_block =~ ^[0-9]+$ ]] || (($dev_ip_block < 1)) || (($dev_ip_block > 254)); do
  read -p "Enter Dev IP Block [1-254]: " dev_ip_block
done

# Confirm settings are correct
echo -e "\nQA APP_ENV\t$qa_app_env";
echo -e "QA Host\t\t$qa_host";
echo -e "Dev IP Block\t$dev_ip_block\n";

read -p "Are these settings correct? " confirm
if [[ $confirm =~ ^[yY] ]]; then
  # Intialize new git repo
  set -e
  rm -rf .git
  git init

  # Initialize submodules
  if [ -e ".gitmodules" ] && [ -s ".gitmodules" ]; then
    git config -f .gitmodules --get-regexp '^submodule\..*\.path$' > tempfile
    while read -u 3 path_key path
    do
        url_key=$(echo $path_key | sed 's/\.path/.url/')
        url=$(git config -f .gitmodules --get "$url_key")
        rm -rf $path; git submodule add $url $path
    done 3<tempfile
    rm tempfile;
  fi

  # Update Vagrantfile
  sed -i s/%QA_APP_ENV%/$qa_app_env/g Vagrantfile
  sed -i s/%QA_HOST%/$qa_host/g Vagrantfile
  sed -i s/%DEV_IP_BLOCK%/$dev_ip_block/g Vagrantfile

  rm initialize.sh
else
  echo "Initialization cancelled"
fi
