import os
from random import randint

fout=open('trdays.csv','w+')
k=0
files=os.listdir('../Train data/rajdhaniroutes/')
for filename in files:
	name=filename[:-4]
	for i in range(0,7):
		if(randint(0,1)==1):
			fout.write(name+','+str(i)+'\n') 
	
files=os.listdir('../Train data/shatabdiroutes/')
for filename in files:
	name=filename[:-4]
	for i in range(0,7):
		if(randint(0,1)==1):
			fout.write(name+','+str(i)+'\n') 

files=os.listdir('../Train data/janshatabdiroutes/')
for filename in files:
	name=filename[:-4]
	for i in range(0,7):
		if(randint(0,1)==1):
			fout.write(name+','+str(i)+'\n') 

files=os.listdir('../Train data/garibrathroutes/')
for filename in files:
	name=filename[:-4]
	for i in range(0,7):
		if(randint(0,1)==1):
			fout.write(name+','+str(i)+'\n') 

files=os.listdir('../Train data/expressroutes/')
for filename in files:
	name=filename[:-4]
	for i in range(0,7):
		if(randint(0,1)==1):
			fout.write(name+','+str(i)+'\n') 

fout.close()
