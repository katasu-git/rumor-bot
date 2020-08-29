# coding: UTF-8
import pymysql.cursors
import connectMySQL
import datetime

def getTodayRumors():
    connection = connectMySQL.connectMySQL()
    d_today = datetime.date.today()

    with connection.cursor() as cursor:
        sql = ("SELECT id, content, fix, fix_tweets, morpheme, updown, created_at FROM rumors WHERE created_at = %s LIMIT 5")
        cursor.execute(sql, d_today)
        contents = cursor.fetchall()
        return contents