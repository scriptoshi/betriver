#!/bin/bash

# First, find all .DS_Store files in the project, excluding ignored paths
find . -type f -name ".DS_Store" \
    ! -path "./node_modules/*" \
    ! -path "./public/build/*" \
    ! -path "./public/hot/*" \
    ! -path "./public/storage/*" \
    ! -path "./storage/*.key" \
    ! -path "./vendor/*" \
    ! -path "./.git/*" \
    -print0 | xargs -0 rm -f

# Remove from git index if they were tracked
git ls-files ".DS_Store" --ignored --exclude-standard -z | xargs -0 git rm --cached

# Add .DS_Store to gitignore if not already present
if ! grep -q ".DS_Store" .gitignore; then
    echo ".DS_Store" >> .gitignore
    echo "**/.DS_Store" >> .gitignore
fi

echo "✨ .DS_Store files have been removed and added to .gitignore"