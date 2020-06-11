# coding: UTF-8
import sys
import MeCab
import urllib2
import csv

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
    #json_open = open('/home/nishimura/public_html/editTsv/rumor_kakimoto.json', 'r')
    f = urllib2.urlopen('http://mednlp.jp/~miyabe/rumorCloud/currentRank.txt')
    line = f.read().splitlines() # 開いたファイルの中身を表示する
    f.close()

    rumors = []
    for l in line:
        if l.split('	') != ['']: #これがないとゴミが入る
            rumors.append( l.split('	') )
    del rumors[0] #一行目は関係ないので削除 , id，　本体，　訂正数　の順の配列になっている

    """
    for r in rumors:
        print str(r[0]) + ":" + r[1]
    """

    return rumors

def writeTsv(array):
    #####　書き込み ####
    f = open('/home/nishimura/public_html/rumor-bot/rumor-background/rumor-nishimura.txt', 'w')
    writer = csv.writer(f, delimiter='\t')
    writer.writerows(array)
    f.close()

    print "書き込み完了！"
    ##################

def main():
    rumors = getRumorsJson()
    anaRumors = returnAnaTexts(rumors) #currentRankに形態素解析の結果を加える
    
    #####　棄却流言を削除　#########
    anaRumorsFixed = []
    for i in range(len(anaRumors)):
        if not( "%" in anaRumors[i][0] ): #棄却ぶんを削除
            anaRumorsFixed.append( anaRumors[i] )
    ############################

    writeTsv(anaRumorsFixed)

main()