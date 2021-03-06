# coding: UTF-8
import pymysql.cursors
import connectMySQL
import datetime
import pushRumors
import getUsers

def getTodayRumors():
    connection = connectMySQL.connectMySQL()
    d_today = datetime.date.today()

    with connection.cursor() as cursor:
        sql = ("SELECT id, content, fix, fix_tweets, morpheme, updown, created_at FROM rumors WHERE created_at = %s LIMIT 5")
        cursor.execute(sql, d_today)
        contents = cursor.fetchall()
        return contents

def getSuddenRiseRumors():
    connection = connectMySQL.connectMySQL()

    with connection.cursor() as cursor:
        sql = ("SELECT id, content, fix, fix_tweets, morpheme, updown, created_at FROM rumors ORDER BY updown DESC LIMIT 5")
        cursor.execute(sql)
        contents = cursor.fetchall()
        return contents

def getRankingRumors():
    connection = connectMySQL.connectMySQL()

    with connection.cursor() as cursor:
        sql = ("SELECT id, content, fix, fix_tweets, morpheme, updown, created_at FROM rumors ORDER BY fix DESC LIMIT 5")
        cursor.execute(sql)
        contents = cursor.fetchall()
        return contents

def getRumorsToSend():
    weekday = datetime.date.today().weekday()
    rumorsToSend = []
    rumorType = ""
    textMessage = ""

    if weekday == 1 or weekday == 4:
        rumorsToSend = getTodayRumors()
        rumorType = "today"
        textMessage = "こんばんは！今日は最新の流言をお伝えするよ！"

    elif weekday == 2 or weekday == 5:
        rumorsToSend = getSuddenRiseRumors()
        rumorType = "suddenRise"
        textMessage = "こんばんは！今日はいま注目度が急上昇している流言をお伝えするよ！"

    elif weekday == 3 or weekday == 6:
        rumorsToSend = getRankingRumors()
        rumorType = "ranking"
        textMessage = "こんばんは！今日は訂正数の多い流言をお伝えするよ！\nもっと見たいときはランキングを開いてね。\nhttps://liff.line.me/1654776413-dpYy83Wb/?id=&path=ranking"

    rumorsAndType = {"rumorsToSend": rumorsToSend, "rumorType": rumorType, "textMessage": textMessage}
    return rumorsAndType

def main():
    rumorsAndType = getRumorsToSend()
    """
    users = getUsers.getUsers()
    sendUsers = []
    for u in users:
        if u['test_group'] == 2:
            sendUsers.append(u['line_user_id'])
    """
    sendUsers = ["Uf811de50a7725a63c181cf7fc8977ae7"]

    if rumorsAndType:
        pushRumors.pushRumors(rumorsAndType['rumorsToSend'], rumorsAndType['rumorType'], rumorsAndType['textMessage'], sendUsers)

main()