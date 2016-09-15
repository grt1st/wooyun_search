#coding:utf-8
import os
import re
import sys
import MySQLdb
from bs4 import BeautifulSoup
path='drops'

pattern0=re.compile(r'>(.*)</h1>')
pattern1=re.compile(r'>\s*(.*)</a>')
pattern2=re.compile(r'>(.*)</')
pattern3=re.compile(r'(.*)(?=-)')
for docs in os.listdir(path):
	if os.path.isdir('drops/'+docs):
		print "目录跳过"
		continue
	#打开文件，提取内容
	doc=open('drops/'+docs,'r')
	html=doc.read()
	doc.close()
	#提取信息
	soup=BeautifulSoup(html,"html.parser")
	title=soup.find_all('h1',class_='entry-title ng-binding')
	if title:
		title=re.findall(pattern0,str(title[0]))
	#title[0]
	else:
		continue
	author=soup.find_all('a',class_='author name ng-binding')
	author=re.findall(pattern1,str(author[0]))
	#author[0]
	time=soup.find_all('time',class_='published ng-binding ng-isolate-scope')
	time=re.findall(pattern2,str(time[0]))
	time1=time[0].replace('/','-')
	
	doc=re.findall(pattern3,docs)
	#doc0
	print title[0],author[0],time1,doc[0],docs
	try:
		conn=MySQLdb.connect(host='localhost',port=3306,user='root',passwd='',db='wooyun',charset='utf8')
		cur=conn.cursor()
		reload(sys)
		sys.setdefaultencoding('utf-8')
		tmp=(title[0],time1,author[0],doc[0],docs)
		cur.execute("INSERT INTO `drops`(`title`,`dates`,`author`,`type`,`doc`) VALUES(%s,%s,%s,%s,%s)",tmp)
		conn.commit()
		cur.close()
		conn.close()
	except MySQLdb.Error,e:
	     print "Mysql Error %d: %s" % (e.args[0], e.args[1])
