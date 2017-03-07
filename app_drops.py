#coding:utf-8
import os
import re
import sys
import MySQLdb
from lxml import etree

path = 'drops'

pattern0 = u' | WooYun知识库'
pattern1 = re.compile(r'(.*)(?=-)')
for docs in os.listdir(path):
	if os.path.isdir('drops/' + docs):
		print "目录跳过"
		continue
	#打开文件，提取内容
	doc = open('drops/' + docs,'r')
	html = doc.read()
	doc.close()

	#提取信息

	xml = etree.HTML(html)

	if(xml.xpath("//title")):
                title=xml.xpath("//title")[0].text.replace(pattern0,'')
        else:
                continue

	author = xml.xpath("//a[@class='author name ng-binding']")[0].text.replace('	','').replace(' ','').replace('\n','')

	time = xml.xpath("//time[@class='published ng-binding ng-isolate-scope']")[0].text.replace('/','-')

	doc = re.findall(pattern1,docs)
	#doc[0]
	print title,author,time,doc[0],docs


	try:
		conn = MySQLdb.connect(host='localhost',port=3306,user='root',passwd='',db='wooyun',charset='utf8')
		cur = conn.cursor()
		reload(sys)
		sys.setdefaultencoding('utf-8')
		tmp = (title,time,author,doc[0],docs)
		cur.execute("INSERT INTO `drops`(`title`,`dates`,`author`,`type`,`doc`) VALUES(%s,%s,%s,%s,%s)", tmp)
		conn.commit()
		cur.close()
		conn.close()
	except MySQLdb.Error,e:
	        print "Mysql Error %d: %s" % (e.args[0], e.args[1])
