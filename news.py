#Function to apply news into database - main function
def medianews():

    # Star collecting info from media
    import mylists

    #Get County and Media List
    medialist = mylists.media_list()
    cntrylist = mylists.country_list()

    # Open database connection and truncate news table to star process
    import mysql.connector
    cnx = mysql.connector.connect(user='root',
                                  password='mypass',
                                  host='localhost',
                                  database='mwatch')

    #import all my functions
    import getData

    #loop media one by one
    for media in medialist:
        myMedid = media[0]
        myMname = media[1]
        myWsite = media[2]
        myMedia = media[3]
        myMedia = myMedia.strip()
        myMLang = media[4]

        #Test for CBC
        if myMedid  > 20:
            continue

        # Create Cursor for mySQL
        mycursor = cnx.cursor()

        # delete all previous records for media - provisional
        #trsql = "DELETE FROM NEWS WHERE media_id =" + str(myMedid)
        #mycursor.execute(trsql)
        #cnx.commit()

        #loop country one by one for each media
        for country in cntrylist:
            myCtyid = country[0]
            myCname = country[1]
            myCnFRE = country[2]
            myCnSPA = country[3]

            #take accent out
            if (myCnFRE == "Brésil"):
                myCnFRA = "Bresil"
            elif (myCnFRE == "République dominicaine"):
                myCnFRA = "Republique dominicaine"
            elif (myCnFRE == "Équateur"):
                myCnFRA = "Equateur"
            elif (myCnFRE == "Jamaïque"):
                myCnFRA = "Jamaique"
            elif (myCnFRE == "Pérou"):
                myCnFRA = "Perou"
            elif (myCnFRE == "Saint-Christophe-et-Niévès"):
                myCnFRA = "Saint-Christophe-et-Nieves"
            elif (myCnFRE == "Trinité-et-Tobago"):
                myCnFRA = "Trinite-et-Tobago"
            else:
                myCnFRA = myCnFRE

            #set search according to language
            if myMLang == "English":
                myCountry = myCname.replace(" ", "+")
                mySearch = myMedia.replace("mwCOUNTRY", myCountry)
            elif myMLang == "French":
                myCountry = myCnFRA.replace(" ", "+")
                mySearch = myMedia.replace("mwCOUNTRY", myCountry)
            else:
                myCountry = myCname.replace(" ", "+")
                mySearch = myMedia.replace("mwCOUNTRY", myCountry)

            #test for venezuela -; proximo pais es el 22
            if (myCtyid > 34):
                continue

            #print media and country
            print(myMname + ' on ' + myCname)

            # Get Data from media into CVS
            getData.fromMedia(myCtyid, myCname, myCnFRA, myMedid, mySearch, myWsite)

    return


#Function to apply news into database - main function
def datanews():

    # Star collecting info from media
    import mylists

    #Get County and Media List
    medialist = mylists.media_list()
    cntrylist = mylists.country_list()

    #import all my functions
    import writeData

    #loop media one by one
    for media in medialist:
        myMedid = media[0]
        myMname = media[1]
        myWsite = media[2]
        myMedia = media[3]
        myMedia = myMedia.strip()
        myMLang = media[4]

        #loop country one by one for each media
        for country in cntrylist:
            myCtyid = country[0]
            myCname = country[1]
            myCnFRE = country[2]
            myCnSPA = country[3]

            #print media and country
            print('Reading ' + myMname + ' on ' + myCname)

            # Get Data from media into CVS
            writeData.readMedia(myCtyid, myCname, myMedid, myWsite)
            print("Done")

    return



#Function to apply TODAYS news into database - main function
def todaynews():

    # Star collecting info from media
    import mylists

    #Get County and Media List
    medialist = mylists.media_list()
    cntrylist = mylists.country_list()

    # Open database connection and truncate news table to star process
    import mysql.connector
    cnx = mysql.connector.connect(user='root',
                                  password='mypass',
                                  host='localhost',
                                  database='mwatch')

    #import all my functions
    import today

    #loop media one by one
    for media in medialist:
        myMedid = media[0]
        myMname = media[1]
        myWsite = media[2]
        myMedia = media[3]
        myMedia = myMedia.strip()
        myMLang = media[4]

        #Test for CBC
        if myMedid  > 15:
            continue

        # Create Cursor for mySQL
        mycursor = cnx.cursor()

        #loop country one by one for each media
        for country in cntrylist:
            myCtyid = country[0]
            myCname = country[1]
            myCnFRE = country[2]
            myCnSPA = country[3]

            #take accent out
            if (myCnFRE == "Brésil"):
                myCnFRA = "Bresil"
            elif (myCnFRE == "République dominicaine"):
                myCnFRA = "Republique dominicaine"
            elif (myCnFRE == "Équateur"):
                myCnFRA = "Equateur"
            elif (myCnFRE == "Jamaïque"):
                myCnFRA = "Jamaique"
            elif (myCnFRE == "Pérou"):
                myCnFRA = "Perou"
            elif (myCnFRE == "Saint-Christophe-et-Niévès"):
                myCnFRA = "Saint-Christophe-et-Nieves"
            elif (myCnFRE == "Trinité-et-Tobago"):
                myCnFRA = "Trinite-et-Tobago"
            else:
                myCnFRA = myCnFRE

            #set search according to language
            if myMLang == "English":
                myCountry = myCname.replace(" ", "+")
                mySearch = myMedia.replace("mwCOUNTRY", myCountry)
            elif myMLang == "French":
                myCountry = myCnFRA.replace(" ", "+")
                mySearch = myMedia.replace("mwCOUNTRY", myCountry)
            else:
                myCountry = myCname.replace(" ", "+")
                mySearch = myMedia.replace("mwCOUNTRY", myCountry)

            #test for venezuela -; proximo pais es el 22
            if (myCtyid > 33):
                continue

            #print media and country
            print(myMname + ' on ' + myCname)

            # Get Data from media into CVS
            today.fromMediaT(myCtyid, myCname, myCnFRA, myMedid, mySearch, myWsite)

    return