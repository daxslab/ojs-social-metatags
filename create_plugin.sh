#! /usr/bin/env sh

rm socialMetatags.tar.gz
mkdir socialMetatags
cp -r * socialMetatags/
rm -R socialMetatags/socialMetatags
tar -cvzf socialMetatags.tar.gz --exclude='*.sh' --exclude='less' --exclude='*.md' --exclude='LICENSE' socialMetatags
rm -R socialMetatags