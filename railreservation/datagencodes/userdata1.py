debug=0
from random import randint

lines = [line.strip() for line in open('male.txt')]
linesf = [line.strip() for line in open('female.txt')]
lineslast = [line.strip() for line in open('sirnames.txt')]

for i in range(0,1500):
	lines[i]+=','+lineslast[i]
	linesf[i]+=','+lineslast[i]

for i in range(0,len(lines)):
	if ord(lines[i][0])>96:
		temp=chr(ord(lines[i][0])-32)+lines[i][1:]
		lines[i]=temp

if debug:
	print lines		


#for males
namemale=[]
for i in range(0,26):
	namemale.append([])
	
for names in lines:
	namemale[ord(names[0])-65].append(names)

if debug:
	print namemale


#for females
for i in range(0,len(linesf)):
	if ord(linesf[i][0])>96:
		temp=chr(ord(linesf[i][0])-32)+linesf[i][1:]
		linesf[i]=temp
#~ print linesf		

namefemale=[]
for i in range(0,26):
	namefemale.append([])
	
for names in linesf:
	namefemale[ord(names[0])-65].append(names)

if debug:
	print namefemale

#Data Generation

datafemale=[]
for i in range(0,26):
	datafemale.append([])

phone=[]
i=0
while(i<3000):
	temp='9'+str(randint(100000000,999999999))
	if (temp not in phone):
		phone.append(temp)
		i+=1
if debug:
	print phone

k=0	
temp=1
for i in range(0,26):
	for j in range(0,len(namefemale[i])):
		datafemale[i].append(str(k)+','+namefemale[i][j][::-1].replace(',','')+','+phone[k]+','+namefemale[i][j]+','+namefemale[i][j].replace(',','')+'@gmail.com'+','+'Female'+','+'1980-02-14')
		k+=1
		
if debug:		
	for i in range(0,26):
		for j in range(0,len(datafemale[i])):
			print datafemale[i][j]

fout=open(filename,'w+')
	
for i in range(0,26):
	filename="./userdata_"+chr(i+97)+".csv"
	for j in range(0,len(datafemale[i])):
		fout.write(datafemale[i][j]+'\n')

fout.close()
#final data for males
datamale=[]
for i in range(0,26):
	datamale.append([])

for i in range(0,26):
	for j in range(0,len(namemale[i])):
		datamale[i].append(str(k+1500)+','+namemale[i][j][::-1].replace(',','')+','+phone[k]+','+namemale[i][j]+','+namemale[i][j].replace(',','')+'@gmail.com'+','+'Male'+','+'1980-11-14')
		k+=1

if debug:		
	for i in range(0,26):
		for j in range(0,len(datamale[i])):
			print datamale[i][j]

fout=open(filename,'w+')
for i in range(0,26):
	filename="./userdata_"+chr(i+97)+".csv"
	for j in range(0,len(datamale[i])):
		fout.write(datamale[i][j]+'\n')
fout.close()
