#Function to Get media data and write CVS for every media
def fromMediaT(myCtyid, myCname, myCnFRA, myMedid, mySearch, myWsite):

    #define output file => not used
    beg = mySearch.find("//") + 2
    end = mySearch.find(".")
    ouf = mySearch[beg:end]
    if ouf == 'www':
        beg = mySearch.find("www.") + 4
        end = mySearch.find(".", mySearch.find(".") + 1)
        ouf = mySearch[beg:end]
    outf = 'C:/Users/Antonio/Documents/BigData/BigProjects/mwatch/data/' + str(myCtyid) + '_' + ouf + '.csv'

    #Goto function according to media
    if (myMedid == 1) or (myMedid == 2) or (myMedid == 4): #sun papers
        TwriteSuns(myCtyid, myCname, mySearch, myWsite, ouf)

    elif (myMedid == 3):  # 'Winnipeg Free Press'
        TwriteWinni(myCtyid, myCname, mySearch, ouf)

    elif (myMedid == 5):  # 'Macleans
        TwriteMAC(myCtyid, myCname, mySearch, ouf)

    elif (myMedid == 6):  # 'Globe and Mail
        TwriteGAM(myCtyid, myCname, mySearch, myWsite, ouf)

    elif (myMedid == 7):  # 'Toronto Star'
        TwriteStar(myCtyid, myCname, mySearch, myWsite, ouf)

    elif (myMedid == 8):  # Journal de Montream
        TwriteJDM(myCtyid, myCnFRA, mySearch, ouf)

    elif (myMedid == 9):  #CBC news
        TwriteCBC(myCtyid, myCname, mySearch, myWsite, ouf)

    elif (myMedid == 10):  # Chronicle Herald Halifax
        TwriteHAX(myCtyid, myCname, mySearch, ouf)

    else:
        print("No paper selected...")

    return

#Function to write from suns papers
def TwriteSuns(cid, cnm, url, wst, ouf):

    import requests
    from bs4 import BeautifulSoup
    r = requests.get(url)
    soup = BeautifulSoup(r.content, 'html.parser')

    # create folder and file
    import csv
    with open('C:/Users/Antonio/Documents/BigData/BigProjects/mwatch/today/' + str(cid) + '_' + ouf + '.csv', 'w') as f:
        writer = csv.writer(f, delimiter='\t', lineterminator='\n', )

        #listing pages to search
        print(url)

        pagelnk = []
        pagelnk = TrefData(soup, cnm)

        for x in pagelnk:
            ws1 = wst[7:len(wst)]
            if x.find(ws1) >= 0:
                writer.writerow([x])

    return

#Function to write from winnipeg paper
def TwriteWinni(cid, cnm, url, ouf):

    import requests
    from bs4 import BeautifulSoup
    r = requests.get(url)
    soup = BeautifulSoup(r.content, 'html.parser')

    # create folder and file
    import csv
    with open('C:/Users/Antonio/Documents/BigData/BigProjects/mwatch/today/' + str(cid) + '_' + ouf + '.csv', 'w') as f:
        writer = csv.writer(f, delimiter='\t', lineterminator='\n', )

        #listing pages to search
        print(url)

        pagelnk = []
        pagelnk = TrefData(soup, cnm)

        for x in pagelnk:
            if x.find("?keywords") < 0:
                writer.writerow([x])

    return


#Function to write from start paper
def TwriteStar(cid, cnm, url, wst, ouf):
    import requests
    from bs4 import BeautifulSoup
    r = requests.get(url)
    soup = BeautifulSoup(r.content, 'html.parser')

    # create folder and file
    import csv
    with open('C:/Users/Antonio/Documents/BigData/BigProjects/mwatch/today/' + str(cid) + '_' + ouf + '.csv', 'w') as f:
        writer = csv.writer(f, delimiter='\t', lineterminator='\n', )

        #listing pages to search
        print(url)

        pagelnk = []
        pagelnk = TrefData(soup, cnm)

        for x in pagelnk:
            myx = wst + x
            writer.writerow([myx])

    return

#Function to write from globe and mail paper
def TwriteGAM(cid, cnm, url, wst, ouf):
    import requests
    from bs4 import BeautifulSoup
    r = requests.get(url)
    soup = BeautifulSoup(r.content, 'html.parser')

    # create folder and file
    import csv
    with open('C:/Users/Antonio/Documents/BigData/BigProjects/mwatch/today/' + str(cid) + '_' + ouf + '.csv', 'w') as f:
        writer = csv.writer(f, delimiter='\t', lineterminator='\n', )

        #listing pages to search
        print(url)

        pagelnk = []
        pagelnk = TrefData(soup, cnm)

        for x in pagelnk:
            if x.find("newsResults") < 0:
                myx = wst + x
                writer.writerow([myx])

    return

#Function to write from macleans
def TwriteMAC(cid, cnm, url, ouf):

    from time import sleep

    from selenium import webdriver
    from selenium.webdriver.common.by import By
    from selenium.webdriver.support.ui import WebDriverWait
    from selenium.webdriver.support import expected_conditions as EC

    from bs4 import BeautifulSoup

    driver = webdriver.PhantomJS(executable_path='C:/Users/Antonio/PhantomJs/phantomjs-2.1.1-windows/bin/phantomjs')
    driver.get(url)

    # wait until button is present
    wait = WebDriverWait(driver, 10)
    wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, ".gsc-selected-option")))

    #Ordering by date
    element = driver.find_element_by_css_selector(".gsc-selected-option")
    driver.execute_script('arguments[0].innerHTML = "Date";', element)

    element = driver.find_element_by_css_selector(".gsc-selected-option")
    element.click()

    # wait until button is present
    wait = WebDriverWait(driver, 10)
    wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, "div.gsc-option-menu-item:nth-child(2) > div:nth-child(1)")))

    element = driver.find_element_by_css_selector("div.gsc-option-menu-item:nth-child(2) > div:nth-child(1)")
    element.click()
    sleep(10)

    #Open file to write csv data
    import csv

    with open('C:/Users/Antonio/Documents/BigData/BigProjects/mwatch/today/' + str(cid) + '_' + ouf + '.csv', 'w') as f:
        writer = csv.writer(f, delimiter='\t', lineterminator='\n', )

        #write first page
        html_page = driver.execute_script("return document.documentElement.innerHTML")
        soup = BeautifulSoup(html_page, 'html.parser')

        #listing page 1
        print(url)

        pagelnk = []
        pagelnk = TrefData(soup, cnm)

        for x in pagelnk:
            writer.writerow([x])

    # close driver
    driver.close()

    return

#Function to write from journal de montreal
def TwriteJDM(cid, cnm, url, ouf):

    from time import sleep
    from selenium import webdriver
    from selenium.webdriver.common.by import By
    from selenium.webdriver.support.ui import WebDriverWait
    from selenium.webdriver.support import expected_conditions as EC

    from bs4 import BeautifulSoup

    driver = webdriver.PhantomJS(executable_path='C:/Users/Antonio/PhantomJs/phantomjs-2.1.1-windows/bin/phantomjs')
    driver.get(url)

    #Open file to write csv data
    import csv
    with open('C:/Users/Antonio/Documents/BigData/BigProjects/mwatch/today/' + str(cid) + '_' + ouf + '.csv', 'w') as f:
        writer = csv.writer(f, delimiter='\t', lineterminator='\n', )

        #write first page
        html_page = driver.execute_script("return document.documentElement.innerHTML")
        soup = BeautifulSoup(html_page, 'html.parser')

        #listing page 1
        print(url)

        #get references
        ul = soup.find('ul', {'class': 'results'})
        children = ul.findChildren()
        for c in children:
            child = str(c)
            if child.find("<li class=") == 0:
                tags = c.find_all("a")

                for t in tags:

                    mycat = t.find("span", class_="small-category-name").text
                    mytit = t.find("span", class_="title").text
                    myspe = t.find("span", class_="specifics").text
                    myhig = t.find("span", class_="highlights").text
                    myref = t.get("href")

                    x = TrefFdat(mytit, myhig, myref, cnm)
                    if x != "nopass":
                        writer.writerow([myref])

    # close driver
    driver.close()

    return


#Function to write from cbc
def TwriteCBC(cid, cnm, url, wst, ouf):

    #Open selenium and phantom
    from selenium import webdriver
    from selenium.webdriver.common.by import By
    from selenium.webdriver.support.ui import WebDriverWait
    from selenium.webdriver.support import expected_conditions as EC

    driver = webdriver.PhantomJS(executable_path='C:/Users/Antonio/PhantomJs/phantomjs-2.1.1-windows/bin/phantomjs')
    driver.get(url)

    try:
        element = WebDriverWait(driver, 10).until(EC.frame_to_be_available_and_switch_to_it("searchFrame"))

    finally:
        html_page = driver.page_source

        #use html_page for getting page total
        from bs4 import BeautifulSoup
        soup = BeautifulSoup(html_page, 'html.parser')

        # create folder and file
        import csv
        with open('C:/Users/Antonio/Documents/BigData/BigProjects/mwatch/today/' + str(cid) + '_' + ouf + '.csv','w') as f:
        #with open('C:/Users/Antonio/Documents/BigData/BigProjects/mwatch/data/' + str(cid) + '_cbc.csv', 'w') as f:
            writer = csv.writer(f, delimiter='\t', lineterminator='\n', )

            #listing pages to search
            nurl = url + "&start=" + str(1)
            print(nurl)

            #first page
            driver.get(nurl)

            try:
                element = WebDriverWait(driver, 10).until(EC.frame_to_be_available_and_switch_to_it("searchFrame"))

            finally:
                html_page = driver.page_source

                #use html_page for getting href content
                #from bs4 import BeautifulSoup
                soup = BeautifulSoup(html_page, 'html.parser')

                pagelnk = []
                pagelnk = TrefData(soup, cnm)

                for x in pagelnk:
                    if x.find(wst) == 0:
                        writer.writerow([x])

    # close driver
    driver.close()

    return


#Function to write from Herald Halifax
def TwriteHAX(cid, cnm, url, ouf):

    import requests
    from bs4 import BeautifulSoup
    r = requests.get(url)
    soup = BeautifulSoup(r.content, 'html.parser')

    # create folder and file
    import csv
    with open('C:/Users/Antonio/Documents/BigData/BigProjects/mwatch/today/' + str(cid) + '_' + ouf + '.csv', 'w') as f:
        writer = csv.writer(f, delimiter='\t', lineterminator='\n', )

        # listing pages to search
        print(url)

        pagelnk = []
        pagelnk = TrefData(soup, cnm)

        for x in pagelnk:
            if x.find("?keywords") < 0:
                writer.writerow([x])

    return


#Function to retrieve csv data from soup
def TrefData(soup, cname):

    import mylists

    links = soup.find_all("a")
    pagelnk = []
    for link in links:
        myref = link.get("href")
        pagelnk.append(myref)

    # eliminate duplicates - list not ordered
    pagelnk = list(set(pagelnk))

    #clean data according to categories
    pageref = []
    for mylink in pagelnk:

        # continue when NONE found
        if mylink is None:
            continue

        wlink = mylink.lower()
        ulink = mylink.upper()
        uctry = cname.upper()

        found_ctry = 1 if (ulink.find(uctry)) > -1 else 0
        found_keys = 1 if any(key in ulink for key in mylists.keys_list(cname)) else 0
        found_catg = 1 if any(cat in wlink for cat in mylists.catg_list()) else 0

        # Check for category and keytags
        if uctry == 'PANAMA' and (ulink.find('PANAMA-PAPERS') > -1 or ulink.find('PANAMAPAPERS') > -1):
            ppage = "nopass"
        else:
            if ((found_ctry == 1) or (found_ctry == 0 and found_keys == 1)) and (found_catg == 1):
                pageref.append(mylink)

    return(pageref)

#Function to retrieve csv data from french papers
def TrefFdat(mytit, myhig, myref, mycnm):

    import mylists

    tlink = mytit.upper()
    rlink = myref.upper()
    hlink = myhig.upper()
    cname = mycnm.upper()

    #check for country name in title or ref
    found_tite = 1 if (tlink.find(cname)) > -1 else 0
    found_refe = 1 if (rlink.find(cname)) > -1 else 0

    # check for key words in title, ref or highligths
    found_keyt = 1 if any(key in tlink for key in mylists.keys_list(mycnm)) else 0
    found_keyr = 1 if any(key in rlink for key in mylists.keys_list(mycnm)) else 0
    found_keyh = 1 if any(key in hlink for key in mylists.keys_list(mycnm)) else 0

    # set criteria
    if (found_tite == 1) or (found_refe == 1):
        ppage = myref
    elif (found_keyt == 1) or (found_keyr == 1) or (found_keyh == 1):
        ppage = myref
    else:
        ppage = "nopass"

    if cname == 'PANAMA' and (rlink.find('PANAMA-PAPERS') > -1 or rlink.find('PANAMAPAPERS') > -1):
        ppage = "nopass"

    return(ppage)

