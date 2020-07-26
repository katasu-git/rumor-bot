# coding: UTF-8
import sys
import urllib.request, urllib.error #python3
import csv

#print(new[0])

def readFile(URL):
    f = open(URL)
    olds = f.read().splitlines()   # ファイル終端まで全て読んだデータを返す
    for index, o in enumerate(olds):
        olds[index] = o.split('	')
    f.close()
    return olds

def main():
    olds = readFile('/home/nishimura/public_html/rumor-bot/rumor-background/rumor-nishimura.txt')
    news = readFile('/home/nishimura/public_html/rumor-bot/rumor-background/rumor-nishimura_sabun.txt')

    for index, n in enumerate(news):
        for o in olds:
            if(n[1] == o[1]):
                difFixsNum = int(n[2]) - int(o[2])
                news[index].append(str(difFixsNum))
                news[index].append("already")
                break
        if len(news[index]) < 7:
            news[index].append(news[index][2])
            news[index].append("new")

    for n in news:
        if int(n[5]) > 0:
            print(n)

main()