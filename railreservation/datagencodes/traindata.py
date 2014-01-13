from random import randint

lines = [line.strip() for line in open('expressnames.txt')]

fout=open('./expressdata.csv','w+')
for i in range(0,len(lines)):
	temp = lines[i]+',EXP,'+str(randint(0,3))+','+'20'+','+'10'+','+'10'+','+'10'
	fout.write(temp+'\n')

fout.close()

lines = [line.strip() for line in open('rajdhaninames.txt')]

fout=open('./rajdhanidata.csv','w+')
for i in range(0,len(lines)):
	temp = lines[i]+',RAJ,'+str(randint(0,3))+','+'20'+','+'10'+','+'10'+','+'10'
	fout.write(temp+'\n')

fout.close()

lines = [line.strip() for line in open('janshatabdinames.txt')]

fout=open('./janshatabdidata.csv','w+')
for i in range(0,len(lines)):
	temp = lines[i]+',JAN,'+str(randint(0,3))+','+'20'+','+'10'+','+'10'+','+'10'
	fout.write(temp+'\n')

fout.close()

lines = [line.strip() for line in open('shatabdinames.txt')]

fout=open('./shatabdidata.csv','w+')
for i in range(0,len(lines)):
	temp = lines[i]+',SHA,'+str(randint(0,3))+','+'20'+','+'10'+','+'10'+','+'10'
	fout.write(temp+'\n')

fout.close()

lines = [line.strip() for line in open('garibrathnames.txt')]

fout=open('./garibrathdata.csv','w+')
for i in range(0,len(lines)):
	temp = lines[i]+',GAR,'+str(randint(0,3))+','+'20'+','+'10'+','+'10'+','+'10'
	fout.write(temp+'\n')

fout.close()
