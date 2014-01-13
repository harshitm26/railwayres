#!/bin/bash


FILES=./"expresstrains"/*.html
for f in $FILES
do
	echo "Processing $f file..."
	# take action on each file. $f store current file name

	sed -n '/Travelled/,/passes/p' $f > ../output.txt

	sed "s/<[^>]\+>/\\n/g" ../output.txt > ../output1.txt

	sed -e '/^[ 	]*$/d' < ../output1.txt > ../output.txt 
	sed -e '/^	*$/d' ../output.txt > ../output1.txt 

	sed "s/[  ]*[	]\+//g" ../output1.txt > ../output.txt

	sed 1d ../output.txt > ../output1.txt
	sed 1d ../output1.txt > ../output.txt

	sed -e '/rain/,/:/d' < ../output.txt > ../output1.txt 
	
	sed '{:q;N;s/\nmin/min/g;t q}' < ../output1.txt > ../output.txt
	sed '{:q;N;s/\n /\n/g;t q}' < ../output.txt > ../output1.txt
	sed '{:q;N;s/ (/(/g;t q}' < ../output1.txt > ../output.txt
	sed '{:q;N;s/)\n/\n/g;t q}' < ../output.txt > ../output1.txt
	sed '{:q;N;s/\n(/\n(/g;t q}' < ../output1.txt > ../output.txt
	sed '{:q;N;s/ //g;t q}' < ../output.txt > ../output1.txt
	sed '{:q;N;s/\n/,/g;t q}' < ../output1.txt > ../output.txt
	sed 's/km,/km\n/g' < ../output.txt > ../output1.txt
	sed 's/(//g' < ../output1.txt > ../output.txt
	sed 's/)//g' < ../output.txt > ../output1.txt
	sed -i '1i1,' ../output1.txt 
	sed '{:q;N;s/1,\n/1,/g;t q}' < ../output1.txt > ../output.txt
	number=$(echo $f | tr -cd '[[:digit:]]')
	echo "Processing $number file..."
	name="./expressroutes/$number.txt"
	cat ../output.txt > $name 
done
