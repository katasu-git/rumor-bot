# coding: UTF-8
import sys
import json
import random

def getRumorsJson():
    #json_open = open('/home/nishimura/public_html/editTsv/rumor_kakimoto.json', 'r')
    f = open('/home/nishimura/public_html/rumor-bot/rumor-background/rumor-nishimura.txt')
    #id　訂正数　元の流言テキスト　分かち書きの結果（スラッシュ区切り）
    data1 = f.read()  # ファイル終端まで全て読んだデータを返す
    f.close()
    line = data1.splitlines()
    rumors = []
    for l in line:
        if l.split('	') != ['']: #これがないとゴミが入る
            rumors.append( l.split('	') )
    return rumors

def getNewRumors(rumors):
    newRumors = []
    for r in rumors:
        if r[6] == 'new':
            newRumors.append(r)
    return newRumors

def main():
    rumors = getRumorsJson()
    # id content num wakachi
    newRumors = getNewRumors(rumors)
    newRumors = random.sample(newRumors, 5)
    for r in newRumors:
        print(r)

main()