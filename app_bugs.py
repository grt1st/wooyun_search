#coding=utf-8
import os
import sys
import MySQLdb
from lxml import etree

path = 'bugs'
#模式
pattern0 = u'漏洞类型：'
pattern1 = u'提交时间：'
pattern2 = u'漏洞标题：'
for docs in os.listdir(path):
	#打开文件，提取内容
	if os.path.isdir('bugs/' + docs):
		print "目录跳过"
		continue
	doc = open('bugs/' + docs,'r')
	html = doc.read()
	doc.close()

	#提取信息

	xml = etree.HTML(html)

	corp = xml.xpath("//h3[@class='wybug_corp']//a")[0].text.replace('	','').replace('\n','')

	author = xml.xpath("//h3[@class='wybug_author']//a")[0].get('href').replace('http://www.wooyun.org/whitehats/','')

	types = xml.xpath("//h3[@class='wybug_type']")[0].text.replace('	','')
	type1 = types.replace(pattern0,'')

	date = xml.xpath("//h3[@class='wybug_date']")[0].text.replace('	','')
	date1 = date.replace(pattern1,'')

	title = xml.xpath("//h3[@class='wybug_title']")[0].text.replace('	','')
	title1 = title.replace(pattern2,'')

	print corp,author,type1,date1,title1

	#连接数据库
	try:
		conn = MySQLdb.connect(host='localhost',port=3306,user='root',passwd='',db='wooyun',charset='utf8')
		cur = conn.cursor()
		reload(sys)
		sys.setdefaultencoding('utf-8')
		tmp = (title1,date1,author,type1,corp,docs)
		cur.execute("INSERT INTO `bugs`(`title`,`dates`,`author`,`type`,`corp`,`doc`) VALUES(%s,%s,%s,%s,%s,%s)", tmp)
		conn.commit()
		cur.close()
		conn.close()
	except MySQLdb.Error,e:
            print "Mysql Error %d: %s" % (e.args[0], e.args[1])
