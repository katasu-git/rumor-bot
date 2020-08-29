# coding: UTF-8
import pymysql.cursors
import datetime

def getWeek():
    weekday = datetime.date.today().weekday()
    return weekday # 0 = monday

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

def getRumors(connection):
    # SQLを実行する
    with connection.cursor() as cursor:
        """
        sql = ("SELECT id, content FROM rumors WHERE created_at BETWEEN %s AND %s")
        cursor.execute(sql, (getWeekAgo(), getToday()))
        """
        sql = ("SELECT id, content, fix FROM rumors ORDER BY updown DESC LIMIT 5")
        cursor.execute(sql)
        contents = cursor.fetchall()
        return contents

def main():
    connection = connectMySQL()
    rumors = getRumors(connection)
    for r in rumors:
        print(r)
    connection.close()

main()