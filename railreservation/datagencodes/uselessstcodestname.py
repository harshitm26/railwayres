import csv
import os

dictionary={}
fout=open('stcodestname.csv','w+')
k=0
files=os.listdir('../Train data/rajdhaniroutes/')
for filename in files:
	fin=open('../Train data/rajdhaniroutes/'+filename)
	for line in fin:
		values=line.split(',')
		lines=[LINE.strip() for LINE in open('stcodestname.csv')]
		temp=values[2]+','+values[1]
		flag=0
		already=0
		for eachline in lines:
			flag=1
			lineval=eachline.split(',')
			for i in range(0,len(lines)):
				temp1=lines[i].split(',')
				if values[2] == temp1[0]: 
					already=1
					break
			if(already==1):
				break		
		if(flag==0 or already==0):
			fout.write(temp+'\n')
	fin.close		
	
k=0
files=os.listdir('../Train data/expressroutes/')
for filename in files:
	fin=open('../Train data/expressroutes/'+filename)
	for line in fin:
		values=line.split(',')
		lines=[LINE.strip() for LINE in open('stcodestname.csv')]
		temp=values[2]+','+values[1]
		flag=0
		already=0
		for eachline in lines:
			flag=1
			lineval=eachline.split(',')
			for i in range(0,len(lines)):
				temp1=lines[i].split(',')
				if values[2] == temp1[0]: 
					already=1
					break
			if(already==1):
				break		
		if(flag==0 or already==0):
			fout.write(temp+'\n')
	fin.close		
	
k=0
files=os.listdir('../Train data/garibrathroutes/')
for filename in files:
	fin=open('../Train data/garibrathroutes/'+filename)
	for line in fin:
		values=line.split(',')
		lines=[LINE.strip() for LINE in open('stcodestname.csv')]
		temp=values[2]+','+values[1]
		flag=0
		already=0
		for eachline in lines:
			flag=1
			lineval=eachline.split(',')
			for i in range(0,len(lines)):
				temp1=lines[i].split(',')
				if values[2] == temp1[0]: 
					already=1
					break
			if(already==1):
				break		
		if(flag==0 or already==0):
			fout.write(temp+'\n')
	fin.close		
	
k=0
files=os.listdir('../Train data/janshatabdiroutes/')
for filename in files:
	fin=open('../Train data/janshatabdiroutes/'+filename)
	for line in fin:
		values=line.split(',')
		lines=[LINE.strip() for LINE in open('stcodestname.csv')]
		temp=values[2]+','+values[1]
		flag=0
		already=0
		for eachline in lines:
			flag=1
			lineval=eachline.split(',')
			for i in range(0,len(lines)):
				temp1=lines[i].split(',')
				if values[2] == temp1[0]: 
					already=1
					break
			if(already==1):
				break		
		if(flag==0 or already==0):
			fout.write(temp+'\n')
	fin.close		
	
k=0
files=os.listdir('../Train data/shatabdiroutes/')
for filename in files:
	fin=open('../Train data/shatabdiroutes/'+filename)
	for line in fin:
		values=line.split(',')
		lines=[LINE.strip() for LINE in open('stcodestname.csv')]
		temp=values[2]+','+values[1]
		flag=0
		already=0
		for eachline in lines:
			flag=1
			lineval=eachline.split(',')
			for i in range(0,len(lines)):
				temp1=lines[i].split(',')
				if values[2] == temp1[0]: 
					already=1
					break
			if(already==1):
				break		
		if(flag==0 or already==0):
			fout.write(temp+'\n')
	fin.close		
	
fout.close	
