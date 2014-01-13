import os

dictionary={}
fout=open('stcodestname.csv','w+')
k=0
files=os.listdir('../Train data/rajdhaniroutes/')
for filename in files:
	fin=open('../Train data/rajdhaniroutes/'+filename)
	for line in fin:
		values=line.split(',')
		if(len(values[2])<=5):
			dictionary[values[2]]=values[1]
	fin.close		

files=os.listdir('../Train data/shatabdiroutes/')
for filename in files:
	fin=open('../Train data/shatabdiroutes/'+filename)
	for line in fin:
		values=line.split(',')
		if(len(values[2])<=5):
			dictionary[values[2]]=values[1]
	fin.close		

files=os.listdir('../Train data/janshatabdiroutes/')
for filename in files:
	fin=open('../Train data/janshatabdiroutes/'+filename)
	for line in fin:
		values=line.split(',')
		if(len(values[2])<=5):
			dictionary[values[2]]=values[1]
	fin.close		

files=os.listdir('../Train data/garibrathroutes/')
for filename in files:
	fin=open('../Train data/garibrathroutes/'+filename)
	for line in fin:
		values=line.split(',')
		if(len(values[2])<=5):
			dictionary[values[2]]=values[1]
	fin.close		

files=os.listdir('../Train data/expressroutes/')
for filename in files:
	fin=open('../Train data/expressroutes/'+filename)
	for line in fin:
		values=line.split(',')
		if(len(values[2])<=5):
			dictionary[values[2]]=values[1]
	fin.close		

for i in dictionary:
	print i+','+dictionary[i]
