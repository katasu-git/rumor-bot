# coding: UTF-8
import sys
import MeCab

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

def returnAnaText(text):
    #辞書の場所を指定する（なぜかフルパスで指定しないと動かない）
    # userdic_path="-d /usr/local/lib/mecab/dic/mecab-ipadic-neologd" #空白削除しないと精度でない？
    m = MeCab.Tagger("-Ochasen")

    nouns = [line.split()[0] for line in m.parse(text).splitlines()
                if "名詞" in line.split()[-1] and not(line.split()[0] in getStopWords()) and not(len(line.split()[0]) == 1)]
                #if "名詞" in line.split()[-1] and not(line.split()[0] in getStopWords())] #配列の最後が「名詞」の時 かつ　ストップワードでない場合

    result = createSlash(nouns)
    return result

def createSlash(texts):
    result = ''
    for t in texts:
        result += ( t + '/' )
    return result[:-1] # 最後のスラッシュは削除

print(returnAnaText(sys.argv[1]))