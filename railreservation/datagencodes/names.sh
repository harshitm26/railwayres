#!/bin/bash


FILES=*.html
for f in $FILES
do
	echo "Processing $f file..."
	# take action on each file. $f store current file name

	number=$(echo $f | tr -cd '[[:digit:]]')
	name=$(echo $f | sed 's/-[0-9]\+-train\.html//g')
	final=$(echo $name | sed 's/_/ /g')
	echo "$number,$final" >> ../janshatabdinames.txt
done
