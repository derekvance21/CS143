import json

with open("nobel-laureates.json", "r") as read_file:
    data = json.load(read_file)

f = open("test.del", "w")

laureates = {}
laureates_file = open("laureates.del", "w")
laureates_attrs = ['id', 'givenName', 'familyName', 'orgName', 'gender', 'originDate', 'originCity', 'originCountry']

# cities = []
# cities_file = open("cities.del", "w")
# cities_keys = ['city', 'country']

prizes = {}
prizes_file = open("prizes.del", "w")
prizes_attrs = ['awardYear', 'category', 'dateAwarded', 'prizeAmount']

# affiliations = {}
# affiliations_file = open("affiliations.del", "w")
# affiliations_keys = ['prize', 'institution']

affiliated = {}
affiliated_file = open("affiliated.del", "w")
affiliated_attrs = ['awardYear', 'category', 'affiliationName', 'affiliationCity', 'affiliationCountry']

win = {}
win_file = open("win.del", "w")
win_attrs = ['lid', 'awardYear', 'category', 'portion', 'sortOrder', 'motivation', 'prizeStatus']

def getAttr(d, keys):
    for key in keys:
        d = d.get(key)
        if not d:
            return d
    return d

def writeAttrs(file, d, attrs):
    for i in range(len(attrs)):
        # if i + 1 < len(attrs):
        #     delimiter = ","
        # else:
        #     delimiter = "\n"
        # # delimiter = "," if (i + 1 < len(attrs)) else "\n"
        field = d[attrs[i]]
        field = "\"" + str(field) + "\"" if field else "\\N"

        file.write(field + ("," if i + 1 < len(attrs) else "\n"))

for laureate in data['laureates']:
    # Laureates
    lid = getAttr(laureate, ['id'])
    givenName = getAttr(laureate, ['givenName', 'en'])
    familyName = getAttr(laureate, ['familyName', 'en'])
    gender = getAttr(laureate, ['gender'])
    orgName = getAttr(laureate, ['orgName', 'en'])
    originKey = 'founded' if orgName else 'birth'
    originDate = getAttr(laureate, [originKey, 'date'])
    originCity = getAttr(laureate, [originKey, 'place', 'city', 'en'])
    originCountry = getAttr(laureate, [originKey, 'place', 'country', 'en'])
    laureateInfo = {'givenName': givenName, 'familyName': familyName, 'gender': gender, 'orgName': orgName, 'originDate': originDate, 'originCity': originCity, 'originCountry': originCountry}
    if lid in laureates:
        print("There are two laureates with lid: " + lid)
        exit(1)
    laureates[lid] = laureateInfo

    laureateInfo['id'] = lid
    writeAttrs(laureates_file, laureateInfo, laureates_attrs)

    # # Cities
    # if originCity and (originCity, originCountry) not in cities:
    #     cities.append((originCity, originCountry))

    # Prizes
    nobelPrizes = getAttr(laureate, ['nobelPrizes'])
    # laureate.get('nobelPrizes')
    if nobelPrizes:
        for nobelPrize in nobelPrizes:
            awardYear = getAttr(nobelPrize, ['awardYear'])
            category = getAttr(nobelPrize, ['category', 'en'])

            sortOrder = getAttr(nobelPrize, ['sortOrder'])
            portion = getAttr(nobelPrize, ['portion'])
            prizeStatus = getAttr(nobelPrize, ['prizeStatus'])
            dateAwarded = getAttr(nobelPrize, ['dateAwarded'])
            motivation = getAttr(nobelPrize, ['motivation', 'en'])
            prizeAmount = getAttr(nobelPrize, ['prizeAmount'])
            prizeInfo = {'dateAwarded': dateAwarded, 'prizeAmount': prizeAmount}

            if not awardYear or not category:
                print("awardYear or category is falsey value: " + str(awardYear) + "," + str(category) + "; lid=" + lid)
                exit(1)
            elif (awardYear, category) not in prizes:
                prizes[(awardYear, category)] = prizeInfo
            elif prizes[(awardYear, category)] != prizeInfo:
                print("Two prizeInfo's exist for " + str((awardYear, category)) + ": " + str(prizes[(awardYear, category)]) + ", " + str(prizeInfo) + "; lid=" + lid)

            # Win
            if lid not in win:
                win[lid] = []
            win[lid].append({'awardYear': awardYear, 'category': category, 'portion': portion, 'sortOrder': sortOrder, 'motivation': motivation, 'prizeStatus': prizeStatus})

            # Affiliations
            prizeAffiliations = getAttr(nobelPrize, ['affiliations'])
            if prizeAffiliations:
                for prizeAffiliation in prizeAffiliations:
                    affiliationName = getAttr(prizeAffiliation, ['name', 'en'])
                    affiliationCity = getAttr(prizeAffiliation, ['city', 'en'])
                    affiliationCountry = getAttr(prizeAffiliation, ['country', 'en'])

                    # # Cities
                    # if affiliationCity and (affiliationCity, affiliationCountry) not in cities:
                    #     cities.append((affiliationCity, affiliationCountry))


                    # Affiliations
                    # if affiliationName not in affiliations:
                    #     affiliations[affiliationName] = affiliationInfo
                    # elif affiliationInfo != affiliations[affiliationName]:
                    #     print("affiliationName: " + affiliationName + " has two affiliationInfo's: " + str(affiliationInfo) + ", " + str(affiliations[affiliationName]))


                    # Affiliated
                    affiliationInfo = {'affiliationName': affiliationName, 'affiliationCity': affiliationCity, 'affiliationCountry': affiliationCountry}
                    if (awardYear, category) not in affiliated:
                        affiliated[(awardYear, category)] = []
                    if affiliationInfo not in affiliated[(awardYear, category)]:
                        affiliated[(awardYear, category)].append(affiliationInfo)
                
    else:
        print("laureate: " + lid + " doesn't have nobelPrizes")

for (awardYear, category) in prizes:
    info = prizes[(awardYear, category)]
    info['awardYear'] = awardYear
    info['category'] = category
    writeAttrs(prizes_file, info, prizes_attrs)

for lid in win:
    for prize in win[lid]:
        prize['lid'] = lid
        writeAttrs(win_file, prize, win_attrs)

for (awardYear, category) in affiliated:
    for affiliation in affiliated[(awardYear, category)]:
        affiliation['awardYear'] = awardYear
        affiliation['category'] = category
        writeAttrs(affiliated_file, affiliation, affiliated_attrs)




laureates_file.close()
prizes_file.close()
win_file.close()
affiliated_file.close()


