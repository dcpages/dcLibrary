#!/bin/bash
host_pattern=^.*\..*$
number_pattern=^[0-9]+$
protocol_pattern=\/\/
ssh_pattern=^.*@.*\..*:.*$

# Check for git
git --version > /dev/null 2>&1
GIT_INSTALLED=$?

[[ $GIT_INSTALLED -ne 0 ]] && { echo "Install git before executing this script."; exit 0; }

# Get input for Git
repo_url=""
while [[ ! $repo_url =~ $ssh_pattern ]]; do
  if [[ $repo_url != "" ]]; then
    echo "Invalid Git SSH URL"
  fi
  read -p "Enter Project Git SSH URL: " repo_url
done

cookbooks_repo_url=""
while [[ ! $cookbooks_repo_url =~ $ssh_pattern ]]; do
  if [[ $cookbooks_repo_url != "" ]]; then
    echo "Invalid Git SSH URL"
  fi
  read -p "Enter Cookbooks Git SSH URL: " cookbooks_repo_url
done

# Get input for Vagrantfile
qa_app_name=""
while [[ $qa_app_name == "" ]]; do
  read -p "Enter QA App Name [project-name-qa]: " qa_app_name
done

qa_host=""
while [[ ! $qa_host =~ $host_pattern ]] || [[ $qa_host =~ $protocol_pattern ]]; do
  read -p "Enter QA Host [domain only]: " qa_host
done

dev_ip_block=""
while [[ ! $dev_ip_block =~ $number_pattern ]] || (($dev_ip_block < 1)) || (($dev_ip_block > 254)); do
  read -p "Enter Dev IP Block [1-254]: " dev_ip_block
done

# Confirm settings are correct
echo -e "\nProject Git URL\t\t$repo_url"
echo -e "Cookbooks Git URL\t$cookbooks_repo_url"
echo -e "QA App Name\t\t$qa_app_name"
echo -e "QA Host\t\t\t$qa_host"
echo -e "Dev IP Block\t\t$dev_ip_block\n"

read -p "Are these settings correct? " confirm
if [[ $confirm =~ ^[yY] ]]; then
  # Intialize new git repo
  set -e
  rm -rf .git
  git init

  git remote add origin $repo_url
  git checkout -b master
  # Add submodules from .gitmodules, if any
  if [ -e ".gitmodules" ] && [ -s ".gitmodules" ]; then
    git config -f .gitmodules --get-regexp '^submodule\..*\.path$' > tempfile
    while read -u 3 path_key path
    do
        url_key=$(echo $path_key | sed 's/\.path/.url/')
        url=$(git config -f .gitmodules --get "$url_key")
        rm -rf $path
        git submodule add $url $path
    done 3<tempfile
    rm tempfile
  fi
  git submodule add $cookbooks_repo_url cookbooks
  git submodule update --init --recursive

  # Update Vagrantfile
  sed -i "" s/%QA_APP_NAME%/$qa_app_name/g Vagrantfile
  sed -i "" s/%QA_HOST%/$qa_host/g Vagrantfile
  sed -i "" s/%DEV_IP_BLOCK%/$dev_ip_block/g Vagrantfile

  rm initialize.sh
else
  echo "Initialization cancelled"
fi
