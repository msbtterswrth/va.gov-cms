#!/usr/bin/env bash

export NVM_DIR="$HOME/.nvm"
[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"
[ -s "$NVM_DIR/bash_completion" ] && \. "$NVM_DIR/bash_completion"
source ~/.bashrc

# Installs the content-build dependencies.

if [ ! -d web/src ]; then
  git clone --filter=tree:0 https://github.com/department-of-veterans-affairs/content-build.git web
else
  echo "Repo content-build already cloned. Updating..."
  git config --global --add safe.directory "$(pwd)/web"
  git -C web reset --hard
  git -C web pull origin $(git -C web rev-parse --abbrev-ref HEAD)
fi

pushd web

nvm install
nvm use
corepack enable
corepack prepare yarn@stable --activate
echo "Node $(node -v)"
echo "NPM $(npm -v)"
echo "Yarn $(yarn -v)"

yarn install-safe --immutable
popd > /dev/null
