#coding=utf-8
import os
import re
import sys
import MySQLdb
from bs4 import BeautifulSoup
path='bugs'
#预编译
pattern0=re.compile(r'<h3.*?class=\'wybug_title\'>.*?漏洞标题：(.*)<img.*?src="/images/credit.png"')
pattern1=re.compile(r'<h3.*?class=\'wybug_date\'>提交时间：(.*)</h3>')
pattern2=re.compile(r'>(.*)</a>')
pattern3=re.compile(r'>(.*)</a>')
pattern4=re.compile(r'：(.*)</h3>')
pattern5=re.compile(r'漏洞标题：(.*)')
for docs in os.listdir(path):
	#打开文件，提取内容
	if os.path.isdir('bugs/'+docs):
		print "目录跳过"
		continue
	doc=open('bugs/'+docs,'r')
	html=doc.read()
	doc.close()
	#提取信息
	soup=BeautifulSoup(html,"html.parser")
	corps=soup.find_all('h3',class_='wybug_corp')
	corps=corps[0].find_all('a')
	corp=corps[0]
	corp=str(corp).replace('	','').replace('\n','')
	authors=soup.find_all('h3',class_='wybug_author')
	authors=authors[0].find_all('a')
	author=authors[0]
	author=str(author).replace('	','')
	types=soup.find_all('h3',class_='wybug_type')
	type0=str(types[0]).replace('	','')
	title=re.findall(pattern0,html)
	if title:
		title1=title[0].replace('	','').replace(' ','')
	else:
		title=soup.find_all('h3',class_='wybug_title')
		title0=title[0].text.encode('utf-8')
		title0=re.findall(pattern5,title0)
		title1=title0[0].replace('\n','').replace(' ','').replace('	','')
	date=re.findall(pattern1,html)
	date1=date[0].replace('	','')
	corp1=re.findall(pattern2,corp)
	author1=re.findall(pattern3,author)
	type1=re.findall(pattern4,type0)
	print title1,date1,author1[0],type1[0],corp1[0]
	#连接数据库
	try:
		conn=MySQLdb.connect(host='localhost',port=3306,user='root',passwd='',db='wooyun',charset='utf8')
		cur=conn.cursor()
		reload(sys)
		sys.setdefaultencoding('utf-8')
		tmp=(title1,date1,author1[0],type1[0],corp1[0],docs)
		cur.execute("INSERT INTO `bugs`(`title`,`dates`,`author`,`type`,`corp`,`doc`) VALUES(%s,%s,%s,%s,%s,%s)",tmp)
		conn.commit()
		cur.close()
		conn.close()
	except MySQLdb.Error,e:
	     print "Mysql Error %d: %s" % (e.args[0], e.args[1])
