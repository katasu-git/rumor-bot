# coding: UTF-8
import sys
import MeCab
#import urllib2 #python2
import urllib.request, urllib.error #python3
import csv
from kutt import kutt #shorURL

def getStopWords():
    #http://svn.sourceforge.jp/svnroot/slothlib/CSharp/Version1/SlothLib/NLP/Filter/StopWord/word/Japanese.txt
    f = open('/home/nishimura/public_html/rumor-bot/rumor-background/stopwordJP.txt')
    data = f.read()  # ファイル終端まで全て読んだデータを返す
    f.close()

    data = data.splitlines()
    stopWords = []
    for d in data:
        if d != "":
            stopWords.append( d )

    return stopWords

def returnAnaTexts(texts):
    #辞書の場所を指定する（なぜかフルパスで指定しないと動かない）
    userdic_path="-d /usr/local/lib/mecab/dic/mecab-ipadic-neologd" #空白削除しないと精度でない？
    m = MeCab.Tagger("-Ochasen")

    anaTexts = []
    for t in texts:
        if not('%' in t[0]): #棄却を弾けていない　多分ループをミスってる
            nouns = [line.split()[0] for line in m.parse(t[1]).splitlines()
                        if "名詞" in line.split()[-1] and not(line.split()[0] in getStopWords())] #配列の最後が「名詞」の時 かつ　ストップワードでない場合
        
        slashNouns = ""
        for i in range(len(nouns)):
            if i != len(nouns) - 1:
                slashNouns = slashNouns + str(nouns[i]) + '/'
            else:
                slashNouns = slashNouns + str(nouns[i])
        #print slashNouns
        text = t
        text.append( slashNouns )
        anaTexts.append( text )
    return anaTexts

def getRumorsJson():
    """
    # python2系の処理 urllib2はpython3にはないらしい
    f = urllib2.urlopen('http://mednlp.jp/~miyabe/rumorCloud/currentRank.txt') #python2
    line = f.read().splitlines() # 開いたファイルの中身を表示する
    f.close()
    """
    url = 'http://mednlp.jp/~miyabe/rumorCloud/currentRank.txt'
    response = urllib.request.urlopen(url)
    data = response.read()
    line = data.splitlines() # 開いたファイルの中身を表示する
    #line = line.decode('utf-8') # なぜかバイト列なので文字列に変換
    #print(line[4])

    rumors = []
    for l in line:
        l = l.decode('utf-8') # なぜかバイト列なので文字列に変換
        if l.split('	') != ['']: #これがないとゴミが入る
            rumors.append( l.split('	') )
    del rumors[0] #一行目は関係ないので削除 , id，　本体，　訂正数　の順の配列になっている

    """
    for r in rumors:
        print str(r[0]) + ":" + r[1]
    """

    return rumors

def getShortURL(longURL):
    f = open('/home/nishimura/public_html/rumor-bot/conf/kutt.txt')
    API = f.read()  # ファイル終端まで全て読んだデータを返す
    obj = kutt.submit(API, longURL, reuse=True) # reuse, customurl and password are OPTIONAL
    shortURL = obj['data']['link']
    return shortURL

def addShortURL(rumors):
    rumorCloudLink = "http://mednlp.jp/~miyabe/rumorCloud/detail_dema2.cgi" #?m=&r=1&n=540
    rumorsWithURL = rumors
    for i in range(len(rumors)):
        uniqueURL = rumorCloudLink + "?m=&r=" + rumors[i][0] + "&n=" + rumors[i][2]
        #uniqueURL = getShortURL(uniqueURL) #urlを短縮
        rumorsWithURL[i].append( uniqueURL )
    print(rumorsWithURL[0])

def writeTsv(array):
    #####　書き込み ####
    f = open('/home/nishimura/public_html/rumor-bot/rumor-background/rumor-nishimura.txt', 'w')
    writer = csv.writer(f, delimiter='\t')
    writer.writerows(array)
    f.close()

    print("書き込み完了！")
    ##################


def main():
    rumors = getRumorsJson()
    anaRumors = returnAnaTexts(rumors) #currentRankに形態素解析の結果を加える
    addShortURL(anaRumors)

    #####　棄却流言を削除　#########
    anaRumorsFixed = []
    for i in range(len(anaRumors)):
        if not( "%" in anaRumors[i][0] ): #棄却ぶんを削除
            anaRumorsFixed.append( anaRumors[i] )
    ############################

    writeTsv(anaRumorsFixed)

main()