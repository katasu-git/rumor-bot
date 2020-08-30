# coding: UTF-8
import pymysql.cursors
import connectMySQL

def getUsers():
    connection = connectMySQL.connectMySQL()

    with connection.cursor() as cursor:
        sql = ("SELECT id, line_user_id , check_sum, check_weekly, check_continue, answer_sum, answer_correct, created_at, last_check_date, gender, test_group FROM chillmo_user")
        cursor.execute(sql)
        contents = cursor.fetchall()
        return contents