#Function to read data from csv files
def readMedia(cid, cnm, mid, wst):

    #define input file => not used
    beg = wst.find("//") + 2
    end = wst.find(".")
    iuf = wst[beg:end]
    if iuf == 'www':
        beg = wst.find("www.") + 4
        end = wst.find(".", wst.find(".") + 1)
        iuf = wst[beg:end]
    inpf = 'C:/Users/Antonio/Documents/BigData/BigProjects/mwatch/data2/' + str(cid) + '_' + iuf + '.csv'

    # read from folder and file
    import os
    import csv

    if os.path.exists('C:/Users/Antonio/Documents/BigData/BigProjects/mwatch/data2/' + str(cid) + '_' + iuf + '.csv'):

        with open('C:/Users/Antonio/Documents/BigData/BigProjects/mwatch/data2/' + str(cid) + '_' + iuf + '.csv', 'r') as f:
            reader = csv.reader(f, delimiter='\t', lineterminator='\n', )

            # star reading file
            for r in reader:
                url = r[0]

                #slip for social media
                if (url[0:7] == 'mailto:' or
                    url[0:23] == 'https://plus.google.com' or
                    url[0:19] == 'https://twitter.com' or
                    url[0:23] == 'http://www.linkedin.com' or
                    url[0:23] == 'http://www.facebook.com' or
                    url[0:20] == 'http://pinterest.com'):
                    print('Wrong link found...')
                    continue

                dhk = chkArticle(url, cid, mid)
                if dhk > 0:
                    print('Duplicate found...')
                    continue
                art = getArticle(url, cid, mid)
                if not art:
                    continue
                writeArticle(art)

    return

#Function to check duplicate article before getting details from web
def chkArticle(url, cid, mid):

    import mysql.connector
    dcnx = mysql.connector.connect(user='root',
                                  password='mypass',
                                  host='localhost',
                                  database='mwatch')

    # Create Cursor for mySQL
    dcursor = dcnx.cursor()

    # Get country list
    dtotal = 0
    dsql = "SELECT count(news_id) FROM NEWS where country_id = %s and nurl = %s"
    dcursor.execute(dsql, (cid, url))
    result = dcursor.fetchone()
    dtotal = result[0]
    dcursor.close()
    dcnx.close()

    return dtotal

#Function to Read Article Properties and return list to update database
def getArticle(url, cid, mid):

    import time

    from newspaper import Article
    import datetime
    from dateutil import parser

    import mylists

    #print(url)

    try:

        article = Article(url)
        article.download()
        article.parse()

        #Getting Ids for DB insert
        my_newss_id = 0
        my_media_id = mid
        my_cntry_id = cid

        #Getting type of article for DB insert
        rlink = url.lower()
        my_types = "none"
        for x in mylists.catg_list():
            if rlink.find(x) > -1:
                my_types = x

        # Getting title for DB insert
        my_title = article.title
        my_nwurl = url

        #getting published date
        if mid == 3 or mid == 9 or mid == 10:
            st_ndate = getPDATE(url, mid)
            my_ndate = parser.parse(st_ndate).strftime('%Y-%m-%d %H:%M:%S')

        else:
            db_ndate = article.publish_date
            st_ndate = str(db_ndate)
            my_ndate = parser.parse(st_ndate).strftime('%Y-%m-%d %H:%M:%S')

        # Getting authors and source for DB insert
        ats = article.authors
        if len(ats) == 1:
            my_source = ""
            my_author = ats[0]
        elif len(ats) == 2:
            my_source = ats[1]
            my_author = ats[0]
        else:
            my_source = ""
            my_author = ""

        my_amail = ""
        my_senti = ""
        db_lastd = datetime.datetime.now()
        st_lastd = str(db_lastd)
        my_lastd = parser.parse(st_lastd).strftime('%Y-%m-%d %H:%M:%S')

        myArticle = []
        myArticle = [
            my_newss_id,
            my_media_id,
            my_cntry_id,
            my_types,
            my_title,
            my_nwurl,
            #str(my_ndate),
            my_ndate,
            my_source,
            my_author,
            my_amail,
            my_senti,
            #str(my_lastd)
            my_lastd ]

    except:
        myArticle = []

    return(myArticle)

#Function to get article and write data in DB
def writeArticle(myArticle):

    # Open database connection and truncate news table to star process
    import mysql.connector

    try:
        cnx = mysql.connector.connect(user='root',
                                    password='mypass',
                                    host='localhost',
                                    database='mwatch')
        #getting cursos for inserts
        mycursor = cnx.cursor()

        insql = "INSERT INTO NEWS VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)"
        mycursor.execute(insql, myArticle)
        cnx.commit()
        print('    Inserting news... ' + str(mycursor.lastrowid))

        # finished, close cursor and database
        mycursor.close()
        cnx.close()

    except mysql.connector.Error as err:
        print("Error {}".format(err))


#Function to get publish dates from Winnipeg and Halifax
def getPDATE(url, mid):
    from dateutil import parser
    from selenium import webdriver
    from selenium.webdriver.common.by import By
    from selenium.webdriver.support.ui import WebDriverWait
    from selenium.webdriver.support import expected_conditions as EC

    driver = webdriver.PhantomJS(executable_path='C:/Users/Antonio/PhantomJs/phantomjs-2.1.1-windows/bin/phantomjs')
    driver.get(url)

    # wait until publish date is present
    if mid == 3: #Wninipeg press
        wait = WebDriverWait(driver, 10)
        wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, "#wallpaperBackground > div:nth-child(1) > div.block > div.article-title > div > p > strong > span")))

        tdate = driver.find_element_by_css_selector("#wallpaperBackground > div:nth-child(1) > div.block > div.article-title > div > p > strong > span").text
        sdd = tdate.strip()

    elif mid == 9: #CBC News
        wait = WebDriverWait(driver, 10)
        wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, ".delimited")))

        tdate = driver.find_element_by_css_selector(".delimited").text
        sdate = tdate.strip()

        beg = -1
        for m in ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]:
            beg = sdate.find(m)
            if beg > -1:
                break

        en1 = -1
        if sdate.find("AM") > 0:
            en1 = sdate.find("AM") + 2

        elif sdate.find("PM") > 0:
            en1 = sdate.find("PM") + 2

        else:
            for e in ["2016", "2015", "2014", "2013", "2012", "2011", "2010", "2009", "2008", "2007", "2006"]:
                en1 = sdate.find(e)
                if en1 > -1:
                    en1 = en1 + 4
                    break

        if en1 < 0:
            end = sdate.len()
        else:
            end = en1

        sdd = sdate[beg:end]

    elif mid == 10: #Halifax press
        wait = WebDriverWait(driver, 10)
        wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, "#container > div.article-header > div.article-header-author > span.publication-timestamp > font:nth-child(1)")))

        parent = driver.find_element_by_css_selector("#container > div.article-header > div.article-header-author > span.publication-timestamp")

        child = parent.find_element_by_tag_name("font")
        tdate = parent.text.replace(child.text, '')
        sdate = tdate.strip()

        beg = 0
        en1 = sdate.find("am")
        en2 = sdate.find("pm")
        if en1 > -1:
            end = en1 + 2
        else:
            end = en2 + 2

        sdd = sdate[beg:end]

    else:
        sdd = ''

    #parse date
    pdate = sdd

    # close driver
    driver.close()

    return(pdate)
