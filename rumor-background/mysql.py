# coding: UTF-8
import pymysql.cursors
from datetime import datetime, timedelta
import datetime

def getToday():
    return datetime.datetime.now().strftime('%Y-%m-%d')

def getYesterday(): 
	today=datetime.date.today() 
	oneday=datetime.timedelta(days=1) 
	yesterday=today-oneday  
	return yesterday

def getWeekAgo():
    weekago = (datetime.datetime.now() - timedelta(weeks=1)).strftime('%Y-%m-%d')
    return weekago

def getYesterdayRumors(connection):
    # SQLを実行する
    with connection.cursor() as cursor:
        # sql = ("SELECT id, content FROM rumors WHERE created_at=%s")
        sql = ("SELECT id, content FROM rumors WHERE created_at BETWEEN %s AND %s")
        cursor.execute(sql, (getWeekAgo(), getToday()))

        # Select結果を取り出す
        contents = cursor.fetchall()
        return contents

def connectMySQL():
    f = open('/home/nishimura/public_html/rumor-bot/conf/dbInfo_python.txt')
    #id　訂正数　元の流言テキスト　分かち書きの結果（スラッシュ区切り）
    data1 = f.read()  # ファイル終端まで全て読んだデータを返す
    f.close()
    line = data1.splitlines()

    connection = pymysql.connect(host='localhost',
                        user=line[0],
                        password=line[1],
                        db=line[2],
                        charset='utf8mb4',
                        cursorclass=pymysql.cursors.DictCursor)
    return connection

def getTodayRumors():
    #json_open = open('/home/nishimura/public_html/editTsv/rumor_kakimoto.json', 'r')
    f = open('/home/nishimura/public_html/rumor-bot/rumor-background/rumor-nishimura.txt')
    # f = open('/home/nishimura/public_html/rumor-bot/rumor-background/dummy.txt')
    #id　訂正数　元の流言テキスト　分かち書きの結果（スラッシュ区切り）
    data1 = f.read()  # ファイル終端まで全て読んだデータを返す
    f.close()
    line = data1.splitlines()
    rumors = []
    for l in line:
        if l.split('	') != ['']: #これがないとゴミが入る
            rumors.append( l.split('	') )
    return rumors

def insertRumor(connection, rumor):
    with connection.cursor() as cursor:
            sql = "INSERT INTO rumors (content, fix, fix_tweets, morpheme, updown, created_at) VALUES (%s, %s, %s, %s, %s, %s)"
            cursor.execute(sql, (rumor[1], int(rumor[2]), rumor[7], rumor[3], int(rumor[5]), getToday()))
            connection.commit()

def updateRumor(connection, id, rumor):
    with connection.cursor() as cursor:
        sql = "UPDATE rumors SET fix = %s WHERE id = %s"
        cursor.execute(sql, (rumor[2], id))
        sql2 = "UPDATE rumors SET updown = %s WHERE id = %s"
        cursor.execute(sql2, (rumor[5], id))
        connection.commit()

def judgeInsertUpdate(yesterdayRumors, todayRumors, connection):
    for tr in todayRumors:
        isExist = False
        for yr in yesterdayRumors:
            if tr[1] == yr['content']:
                # updateRumor(connection, id, tr)
                isExist = True
                break
        if(isExist):
            updateRumor(connection, yr['id'], tr)
        else:
            insertRumor(connection, tr)

def main():
    connection = connectMySQL()
    yesterdayRumors = getYesterdayRumors(connection)
    todayRumors = getTodayRumors()
    judgeInsertUpdate(yesterdayRumors, todayRumors, connection)
    connection.close()

main()