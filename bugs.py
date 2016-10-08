#coding=utf-8
import MySQLdb
import sys
#连接数据库
try:
	conn=MySQLdb.connect(host='localhost',port=3307,user='root',passwd='',db='wooyun',charset='utf8')
	cur=conn.cursor()

	reload(sys)
	sys.setdefaultencoding('utf-8')

	cur.execute("SELECT * from bugs where author like '%[email%'")
	items=cur.fetchall()
	for item in items:
		doc=(item[5])
		cur.execute("UPDATE bugs set author='[email protected]' where doc='%s'"%doc) 
		print "success in doc="%doc
	conn.commit()
	cur.close()
	conn.close()
except MySQLdb.Error,e:
	print "Mysql Error %d: %s" % (e.args[0], e.args[1])