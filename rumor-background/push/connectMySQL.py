# coding: UTF-8
import pymysql.cursors

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