/**
 * ANZGO-3171 Added by John Renzo S. Sunico, 01/24/2018
 * Preload State List
 */

var ccm_attributeTypeAddressStates;
var ccm_attributeTypeAddressStatesTextList = '\
US:AL:Alabama|\
US:AK:Alaska|\
US:AZ:Arizona|\
US:AR:Arkansas|\
US:CA:California|\
US:CO:Colorado|\
US:CT:Connecticut|\
US:DE:Delaware|\
US:FL:Florida|\
US:GA:Georgia|\
US:HI:Hawaii|\
US:ID:Idaho|\
US:IL:Illinois|\
US:IN:Indiana|\
US:IA:Iowa|\
US:KS:Kansas|\
US:KY:Kentucky|\
US:LA:Louisiana|\
US:ME:Maine|\
US:MD:Maryland|\
US:MA:Massachusetts|\
US:MI:Michigan|\
US:MN:Minnesota|\
US:MS:Mississippi|\
US:MO:Missouri|\
US:MT:Montana|\
US:NE:Nebraska|\
US:NV:Nevada|\
US:NH:New Hampshire|\
US:NJ:New Jersey|\
US:NM:New Mexico|\
US:NY:New York|\
US:NC:North Carolina|\
US:ND:North Dakota|\
US:OH:Ohio|\
US:OK:Oklahoma|\
US:OR:Oregon|\
US:PA:Pennsylvania|\
US:RI:Rhode Island|\
US:SC:South Carolina|\
US:SD:South Dakota|\
US:TN:Tennessee|\
US:TX:Texas|\
US:UT:Utah|\
US:VT:Vermont|\
US:VA:Virginia|\
US:WA:Washington|\
US:DC:Washington, DC|\
US:WV:West Virginia|\
US:WI:Wisconsin|\
US:WY:Wyoming|\
CA:AB:Alberta|\
CA:BC:British Columbia|\
CA:MB:Manitoba|\
CA:NB:New Brunswick|\
CA:NL:Newfoundland and Labrador|\
CA:NT:Northwest Territories|\
CA:NS:Nova Scotia|\
CA:NU:Nunavut|\
CA:ON:Ontario|\
CA:PE:Prince Edward Island|\
CA:QC:Quebec|\
CA:SK:Saskatchewan|\
CA:YT:Yukon|\
AU:AAT:Australian Antarctic Territory|\
AU:ACT:Australian Capital Territory|\
AU:NSW:New South Wales|\
AU:NT:Northern Territory|\
AU:QLD:Queensland|\
AU:SA:South Australia|\
AU:TAS:Tasmania|\
AU:VIC:Victoria|\
AU:WA:Western Australia|\
DE:BW:Baden-Württemberg|\
DE:BY:Bayern|\
DE:BE:Berlin|\
DE:BB:Brandenburg|\
DE:HB:Bremen|\
DE:HH:Hamburg|\
DE:HE:Hessen|\
DE:MV:Mecklenburg-Vorpommern|\
DE:NI:Niedersachsen|\
DE:NW:Nordrhein-Westfalen|\
DE:RP:Rheinland-Pfalz|\
DE:SL:Saarland|\
DE:SN:Sachsen|\
DE:ST:Sachsen-Anhalt|\
DE:SH:Schleswig-Holstein|\
DE:TH:Thüringen|\
FR:01:Ain|\
FR:02:Aisne|\
FR:03:Allier|\
FR:06:Alpes-Maritimes|\
FR:04:Alpes-de-Haute-Provence|\
FR:08:Ardennes|\
FR:07:Ardèche|\
FR:09:Ariège|\
FR:10:Aube|\
FR:11:Aude|\
FR:12:Aveyron|\
FR:67:Bas-Rhin|\
FR:13:Bouches-du-Rhône|\
FR:14:Calvados|\
FR:15:Cantal|\
FR:16:Charente|\
FR:17:Charente-Maritime|\
FR:18:Cher|\
FR:19:Corrèze|\
FR:2A:Corse-du-Sud|\
FR:23:Creuse|\
FR:22:Côte-d\'Armor|\
FR:21:Côte-d\'Or|\
FR:79:Deux-Sèvres|\
FR:24:Dordogne|\
FR:25:Doubs|\
FR:26:Drôme|\
FR:91:Essonne|\
FR:27:Eure|\
FR:28:Eure-et-Loir|\
FR:29:Finistère|\
FR:30:Gard|\
FR:32:Gers|\
FR:33:Gironde|\
FR:68:Haut-Rhin|\
FR:05:Haute-Alpes|\
FR:2B:Haute-Corse|\
FR:31:Haute-Garonne|\
FR:43:Haute-Loire|\
FR:52:Haute-Marne|\
FR:74:Haute-Savoie|\
FR:70:Haute-Saône|\
FR:87:Haute-Vienne|\
FR:65:Hautes-Pyrénées|\
FR:92:Hauts-de-Seine|\
FR:34:Hérault|\
FR:35:Ille-et-Vilaine|\
FR:36:Indre|\
FR:37:Indre-et-Loire|\
FR:38:Isère|\
FR:39:Jura|\
FR:40:Landes|\
FR:41:Loir-et-Cher|\
FR:42:Loire|\
FR:44:Loire-Atlantique|\
FR:45:Loiret|\
FR:46:Lot|\
FR:47:Lot-et-Garonne|\
FR:48:Lozère|\
FR:49:Maine-et-Loire|\
FR:50:Manche|\
FR:51:Marne|\
FR:53:Mayenne|\
FR:54:Meurthe-et-Moselle|\
FR:55:Meuse|\
FR:56:Morbihan|\
FR:57:Moselle|\
FR:58:Nièvre|\
FR:59:Nord|\
FR:60:Oise|\
FR:61:Orne|\
FR:75:Paris|\
FR:62:Pas-de-Calais|\
FR:63:Puy-de-Dôme|\
FR:64:Pyrénées-Atlantiques|\
FR:66:Pyrénées-Orientales|\
FR:69:Rhône|\
FR:72:Sarthe|\
FR:73:Savoie|\
FR:71:Saône-et-Loire|\
FR:76:Seine-Maritime|\
FR:93:Seine-Saint-Denis|\
FR:77:Seine-et-Marne|\
FR:80:Somme|\
FR:81:Tarn|\
FR:82:Tarn-et-Garonne|\
FR:90:Territoire de Belfort|\
FR:95:Val-d\'Oise|\
FR:94:Val-de-Marne|\
FR:83:Var|\
FR:84:Vaucluse|\
FR:85:Vendée|\
FR:86:Vienne|\
FR:88:Vosges|\
FR:89:Yonne|\
FR:78:Yvelines|\
UK:ARBERD:Aberdeenshire|\
UK:ANGLES:Anglesey|\
UK:ANGUS:Angus|\
UK:ARGYLL:Argyllshire|\
UK:AYRSH:Ayrshire|\
UK:BANFF:Banffshire|\
UK:BEDS:Bedfordshire|\
UK:BERKS:Berkshire|\
UK:BERWICK:Berwickshire|\
UK:BRECK:Brecknockshire|\
UK:BUCKS:Buckinghamshire|\
UK:BUTE:Buteshire|\
UK:CAERN:Caernarfonshire|\
UK:CAITH:Caithness|\
UK:CAMBS:Cambridgeshire|\
UK:CARDIG:Cardiganshire|\
UK:CARMA:Carmathenshire|\
UK:CHESH:Cheshire|\
UK:CLACKM:Clackmannanshire|\
UK:CORN:Cornwall|\
UK:CROMART:Cromartyshire|\
UK:CUMB:Cumberland|\
UK:DENBIG:Denbighshire|\
UK:DERBY:Derbyshire|\
UK:DEVON:Devon|\
UK:DORSET:Dorset|\
UK:DUMFR:Dumfriesshire|\
UK:DUNBART:Dunbartonshire|\
UK:DURHAM:Durham|\
UK:EASTL:East Lothian|\
UK:ESSEX:Essex|\
UK:FIFE:Fife|\
UK:FLINTS:Flintshire|\
UK:GLAMO:Glamorgan|\
UK:GLOUS:Gloucestershire|\
UK:HANTS:Hampshire|\
UK:HEREF:Herefordshire|\
UK:HERTS:Hertfordshire|\
UK:HUNTS:Huntingdonshire|\
UK:INVERN:Inverness-shire|\
UK:KENT:Kent|\
UK:KINCARD:Kincardineshire|\
UK:KINROSS:Kinross-shire|\
UK:KIRKCUD:Kircudbrightshire|\
UK:LANARK:Lanarkshire|\
UK:LANCS:Lancashire|\
UK:LEICS:Leicestershire|\
UK:LINCS:Lincolnshire|\
UK:LONDON:London|\
UK:MERION:Merioneth|\
UK:MERSEYSIDE:Merseyside|\
UK:MIDDLE:Middlesex|\
UK:MIDLOTH:Midlothian|\
UK:MONTG:Mongtomeryshire|\
UK:MONMOUTH:Monmouthshire|\
UK:MORAY:Morayshire|\
UK:NAIRN:Nairnshire|\
UK:NORF:Norfolk|\
UK:NHANTS:Northamptonshire|\
UK:NTHUMB:Northumberland|\
UK:NOTTS:Nottinghamshire|\
UK:ORKNEY:Orkeny|\
UK:OXON:Oxfordshire|\
UK:PEEBLESS:Peeblesshire|\
UK:PEMBR:Pembrokeshire|\
UK:PERTH:Perthshire|\
UK:RADNOR:Radnorshire|\
UK:RENFREW:Renfrewshire|\
UK:ROSSSH:Ross-shire|\
UK:ROXBURGH:Roxburghshire|\
UK:RUTL:Rutland|\
UK:SELKIRK:Selkirkshire|\
UK:SHETLAND:Shetland|\
UK:SHROPS:Shropshire|\
UK:SOM:Somerset|\
UK:STAFFS:Staffordshire|\
UK:STIRLING:Stirlingshire|\
UK:SUFF:Suffolk|\
UK:SURREY:Surrey|\
UK:SUSS:Sussex|\
UK:SUTHER:Sutherland|\
UK:WARKS:Warwickshire|\
UK:WESTL:West Lothian|\
UK:WESTMOR:Westmorland|\
UK:WIGTOWN:Wigtownshire|\
UK:WILTS:Wiltshire|\
UK:WORCES:Worcestershire|\
UK:YORK:Yorkshire|\
IE:CO ANTRIM:County Antrim|\
IE:CO ARMAGH:County Armagh|\
IE:CO CARLOW:County Carlow|\
IE:CO CAVAN:County Cavan|\
IE:CO CLARE:County Clare|\
IE:CO CORK:County Cork|\
IE:CO DONEGAL:County Donegal|\
IE:CO DOWN:County Down|\
IE:CO DUBLIN:County Dublin|\
IE:CO FERMANAGH:County Fermanagh|\
IE:CO GALWAY:County Galway|\
IE:CO KERRY:County Kerry|\
IE:CO KILDARE:County Kildare|\
IE:CO KILKENNY:County Kilkenny|\
IE:CO LAOIS:County Laois|\
IE:CO LEITRIM:County Leitrim|\
IE:CO LIMERICK:County Limerick|\
IE:CO DERRY:County Londonderry|\
IE:CO LONGFORD:County Longford|\
IE:CO LOUTH:County Louth|\
IE:CO MAYO:County Mayo|\
IE:CO MEATH:County Meath|\
IE:CO MONAGHAN:County Monaghan|\
IE:CO OFFALY:County Offaly|\
IE:CO ROSCOMMON:County Roscommon|\
IE:CO SLIGO:County Sligo|\
IE:CO TIPPERARY:County Tipperary|\
IE:CO TYRONE:County Tyrone|\
IE:CO WATERFORD:County Waterford|\
IE:CO WESTMEATH:County Westmeath|\
IE:CO WEXFORD:County Wexford|\
IE:CO WICKLOW:County Wicklow|\
NL:DR:Drenthe|\
NL:FL:Flevoland|\
NL:FR:Friesland|\
NL:GL:Gelderland|\
NL:GR:Groningen|\
NL:LB:Limburg|\
NL:NB:Noord Brabant|\
NL:NH:Noord Holland|\
NL:OV:Overijssel|\
NL:UT:Utrecht|\
NL:ZL:Zeeland|\
NL:ZH:Zuid Holland|\
BR:AC:Acre|\
BR:AL:Alagoas|\
BR:AP:Amapa|\
BR:AM:Amazonas|\
BR:BA:Baia|\
BR:CE:Ceara|\
BR:DF:Distrito Federal|\
BR:ES:Espirito Santo|\
BR:FN:Fernando de Noronha|\
BR:GO:Goias|\
BR:MA:Maranhao|\
BR:MT:Mato Grosso|\
BR:MS:Mato Grosso do Sul|\
BR:MG:Minas Gerais|\
BR:PA:Para|\
BR:PB:Paraiba|\
BR:PR:Parana|\
BR:PE:Pernambuco|\
BR:PI:Piaui|\
BR:RN:Rio Grande do Norte|\
BR:RS:Rio Grande do Sul|\
BR:RJ:Rio de Janeiro|\
BR:RO:Rondonia|\
BR:RR:Roraima|\
BR:SC:Santa Catarina|\
BR:SP:Sao Paulo|\
BR:SE:Sergipe|\
BR:TO:Tocatins|\
IT:AG:Agrigento|\
IT:AL:Alessandria|\
IT:AN:Ancona|\
IT:AO:Aosta|\
IT:AR:Arezzo|\
IT:AP:Ascoli Piceno|\
IT:AT:Asti|\
IT:AV:Avellino|\
IT:BA:Bari|\
IT:BT:Barletta-Andria-Trani|\
IT:BL:Belluno|\
IT:BN:Benevento|\
IT:BG:Bergamo|\
IT:BI:Biella|\
IT:BO:Bologna|\
IT:BZ:Bolzano|\
IT:BS:Brescia|\
IT:BR:Brindisi|\
IT:CA:Cagliari|\
IT:CL:Caltanissetta|\
IT:CB:Campobasso|\
IT:CI:Carbonia-Iglesias|\
IT:CE:Caserta|\
IT:CT:Catania|\
IT:CZ:Catanzaro|\
IT:CH:Chieti|\
IT:CO:Como|\
IT:CS:Cosenza|\
IT:CR:Cremona|\
IT:KR:Crotone|\
IT:CN:Cuneo|\
IT:EN:Enna|\
IT:FM:Fermo|\
IT:FE:Ferrara|\
IT:FI:Firenze|\
IT:FG:Foggia|\
IT:FC:Forlì-Cesena|\
IT:FR:Frosinone|\
IT:GE:Genova|\
IT:GO:Gorizia|\
IT:GR:Grosseto|\
IT:IM:Imperia|\
IT:IS:Isernia|\
IT:SP:La Spezia|\
IT:LT:Latina|\
IT:LE:Lecce|\
IT:LC:Lecco|\
IT:LI:Livorno|\
IT:LO:Lodi|\
IT:LU:Lucca|\
IT:AQ:L’Aquila|\
IT:MC:Macerata|\
IT:MN:Mantova|\
IT:MS:Massa e Carrara|\
IT:MT:Matera|\
IT:VS:Medio Campidano|\
IT:ME:Messina|\
IT:MI:Milano|\
IT:MO:Modena|\
IT:MB:Monza e Brianza|\
IT:NA:Napoli|\
IT:NO:Novara|\
IT:NU:Nuoro|\
IT:OG:Ogliastra|\
IT:OT:Olbia-Tempio|\
IT:OR:Oristano|\
IT:PD:Padova|\
IT:PA:Palermo|\
IT:PR:Parma|\
IT:PV:Pavia|\
IT:PG:Perugia|\
IT:PU:Pesaro e Urbino|\
IT:PE:Pescara|\
IT:PC:Piacenza|\
IT:PI:Pisa|\
IT:PT:Pistoia|\
IT:PN:Pordenone|\
IT:PZ:Potenza|\
IT:PO:Prato|\
IT:RG:Ragusa|\
IT:RA:Ravenna|\
IT:RC:Reggio Calabria|\
IT:RE:Reggio Emilia|\
IT:RI:Rieti|\
IT:RN:Rimini|\
IT:RM:Roma|\
IT:RO:Rovigo|\
IT:SA:Salerno|\
IT:SS:Sassari|\
IT:SV:Savona|\
IT:SI:Siena|\
IT:SR:Siracusa|\
IT:SO:Sondrio|\
IT:TA:Taranto|\
IT:TE:Teramo|\
IT:TR:Terni|\
IT:TO:Torino|\
IT:TP:Trapani|\
IT:TN:Trento|\
IT:TV:Treviso|\
IT:TS:Trieste|\
IT:UD:Udine|\
IT:VA:Varese|\
IT:VE:Venezia|\
IT:VB:Verbano-Cusio-Ossola|\
IT:VC:Vercelli|\
IT:VR:Verona|\
IT:VV:Vibo Valentia|\
IT:VI:Vicenza|\
IT:VT:Viterbo|\
JP:01:北海道|\
JP:02:青森県|\
JP:03:岩手県|\
JP:04:宮城県|\
JP:05:秋田県|\
JP:06:山形県|\
JP:07:福島県|\
JP:08:茨城県|\
JP:09:栃木県|\
JP:10:群馬県|\
JP:11:埼玉県|\
JP:12:千葉県|\
JP:13:東京都|\
JP:14:神奈川県|\
JP:15:新潟県|\
JP:16:富山県|\
JP:17:石川県|\
JP:18:福井県|\
JP:19:山梨県|\
JP:20:長野県|\
JP:21:岐阜県|\
JP:22:静岡県|\
JP:23:愛知県|\
JP:24:三重県|\
JP:25:滋賀県|\
JP:26:京都府|\
JP:27:大阪府|\
JP:28:兵庫県|\
JP:29:奈良県|\
JP:30:和歌山県|\
JP:31:鳥取県|\
JP:32:島根県|\
JP:33:岡山県|\
JP:34:広島県|\
JP:35:山口県|\
JP:36:徳島県|\
JP:37:香川県|\
JP:38:愛媛県|\
JP:39:高知県|\
JP:40:福岡県|\
JP:41:佐賀県|\
JP:42:長崎県|\
JP:43:熊本県|\
JP:44:大分県|\
JP:45:宮崎県|\
JP:46:鹿児島県|\
JP:47:沖縄県|\
GB:ARBERD:Aberdeenshire|\
GB:ANGLES:Anglesey|\
GB:ANGUS:Angus|\
GB:ARGYLL:Argyllshire|\
GB:AYRSH:Ayrshire|\
GB:BANFF:Banffshire|\
GB:BEDS:Bedfordshire|\
GB:BERKS:Berkshire|\
GB:BERWICK:Berwickshire|\
GB:BRECK:Brecknockshire|\
GB:BUCKS:Buckinghamshire|\
GB:BUTE:Buteshire|\
GB:CAERN:Caernarfonshire|\
GB:CAITH:Caithness|\
GB:CAMBS:Cambridgeshire|\
GB:CARDIG:Cardiganshire|\
GB:CARMA:Carmathenshire|\
GB:CHESH:Cheshire|\
GB:CLACKM:Clackmannanshire|\
GB:CORN:Cornwall|\
GB:CROMART:Cromartyshire|\
GB:CUMB:Cumberland|\
GB:DENBIG:Denbighshire|\
GB:DERBY:Derbyshire|\
GB:DEVON:Devon|\
GB:DORSET:Dorset|\
GB:DUMFR:Dumfriesshire|\
GB:DUNBART:Dunbartonshire|\
GB:DURHAM:Durham|\
GB:EASTL:East Lothian|\
GB:ESSEX:Essex|\
GB:FIFE:Fife|\
GB:FLINTS:Flintshire|\
GB:GLAMO:Glamorgan|\
GB:GLOUS:Gloucestershire|\
GB:HANTS:Hampshire|\
GB:HEREF:Herefordshire|\
GB:HERTS:Hertfordshire|\
GB:HUNTS:Huntingdonshire|\
GB:INVERN:Inverness-shire|\
GB:KENT:Kent|\
GB:KINCARD:Kincardineshire|\
GB:KINROSS:Kinross-shire|\
GB:KIRKCUD:Kircudbrightshire|\
GB:LANARK:Lanarkshire|\
GB:LANCS:Lancashire|\
GB:LEICS:Leicestershire|\
GB:LINCS:Lincolnshire|\
GB:LONDON:London|\
GB:MERION:Merioneth|\
GB:MERSEYSIDE:Merseyside|\
GB:MIDDLE:Middlesex|\
GB:MIDLOTH:Midlothian|\
GB:MONTG:Mongtomeryshire|\
GB:MONMOUTH:Monmouthshire|\
GB:MORAY:Morayshire|\
GB:NAIRN:Nairnshire|\
GB:NORF:Norfolk|\
GB:NHANTS:Northamptonshire|\
GB:NTHUMB:Northumberland|\
GB:NOTTS:Nottinghamshire|\
GB:ORKNEY:Orkeny|\
GB:OXON:Oxfordshire|\
GB:PEEBLESS:Peeblesshire|\
GB:PEMBR:Pembrokeshire|\
GB:PERTH:Perthshire|\
GB:RADNOR:Radnorshire|\
GB:RENFREW:Renfrewshire|\
GB:ROSSSH:Ross-shire|\
GB:ROXBURGH:Roxburghshire|\
GB:RUTL:Rutland|\
GB:SELKIRK:Selkirkshire|\
GB:SHETLAND:Shetland|\
GB:SHROPS:Shropshire|\
GB:SOM:Somerset|\
GB:STAFFS:Staffordshire|\
GB:STIRLING:Stirlingshire|\
GB:SUFF:Suffolk|\
GB:SURREY:Surrey|\
GB:SUSS:Sussex|\
GB:SUTHER:Sutherland|\
GB:WARKS:Warwickshire|\
GB:WESTL:West Lothian|\
GB:WESTMOR:Westmorland|\
GB:WIGTOWN:Wigtownshire|\
GB:WILTS:Wiltshire|\
GB:WORCES:Worcestershire|\
GB:YORK:Yorkshire|\
';

$(function () {
    ccm_attributeTypeAddressStates = ccm_attributeTypeAddressStatesTextList.split('|');
});

ccm_attributeTypeAddressSelectCountry = function (cls, country) {
    var ss = $('.' + cls + ' .ccm-attribute-address-state-province select');
    var si = $('.' + cls + ' .ccm-attribute-address-state-province input[type=text]');

    var foundStateList = false;
    ss.html("");
    for (j = 0; j < ccm_attributeTypeAddressStates.length; j++) {
        var sa = ccm_attributeTypeAddressStates[j].split(':');
        if (jQuery.trim(sa[0]) == country) {
            if (!foundStateList) {
                foundStateList = true;
                si.attr('name', 'inactive_' + si.attr('ccm-attribute-address-field-name'));
                si.hide();
                ss.append('<option value="">Choose State/Province</option>');
            }
            ss.show();
            ss.attr('name', si.attr('ccm-attribute-address-field-name'));
            ss.append('<option value="' + jQuery.trim(sa[1]) + '">' + jQuery.trim(sa[2]) + '</option>');
        }
    }

    if (!foundStateList) {
        ss.attr('name', 'inactive_' + si.attr('ccm-attribute-address-field-name'));
        ss.hide();
        si.show();
        si.attr('name', si.attr('ccm-attribute-address-field-name'));
    }
}

ccm_setupAttributeTypeAddressSetupStateProvinceSelector = function (cls) {

    var cs = $('.' + cls + ' .ccm-attribute-address-country select');
    cs.change(function () {
        var v = $(this).val();
        ccm_attributeTypeAddressSelectCountry(cls, v);
    });

    if (cs.attr('ccm-passed-value') != '') {
        $(function () {
            cs.find('option[value="' + cs.attr('ccm-passed-value') + '"]').attr('selected', true);
            ccm_attributeTypeAddressSelectCountry(cls, cs.attr('ccm-passed-value'));
            var ss = $('.' + cls + ' .ccm-attribute-address-state-province select');
            if (ss.attr('ccm-passed-value') != '') {
                ss.find('option[value="' + ss.attr('ccm-passed-value') + '"]').attr('selected', true);
            }
        });
    }
}
