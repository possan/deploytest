#!/bin/sh

# I = Commit ID
I=$1
# U = Github display URL
U=$2
# G = Git clone URL
G=$3
# B = Git branch 
B=$4
# Z = Zip mirror
Z="https://github.com/possan/deploytest/zipball/stable"
# M = Mail to this person when updated.
M="possan@possan.se"
# R = Target root
R=/var/www/deploytest
# C = Clones directory
C=$R/clones
# T = Target clone directory
T=$C/`date +%s`
cd $R
echo $T
rm -rf $T
pushd .
git clone $G $T
cd $T
git checkout $B
popd
# link the active directory
rm -f current
ln -s $T current 
# notify the admin
echo -e "Subject: Website deployed\n\nCommit ID $I (to folder $T)\nGithub link: $U\nGit clone URL: $G\n\n" > /tmp/mail
cat /tmp/mail | sendmail $M

