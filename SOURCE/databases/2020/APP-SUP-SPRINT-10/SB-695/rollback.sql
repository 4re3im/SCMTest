--- STEP 1: Rollback the inserted proofs (access codes)
DELETE FROM hub.permissions WHERE proof IN(
'FPYD-YKWN-MXVL-PSPW',
'QZLN-WSNQ-QFRB-XWBK',
'WJZG-LYZV-NCPG-PDHJ',
'JJNK-RTJX-SVGD-WQBD',
'PLXB-GCFQ-LCZR-BHYM',
'QTYX-MGGX-FHSP-QLBN',
'FGGY-RVML-CPPD-HBXN',
'KKSM-HYBB-NTHV-SZTC',
'DTGJ-BFFW-JHGF-MZPQ',
'RXVC-FXDZ-DVHY-YRGV',
'FDMC-DFQF-GCSY-FFYB',
'FMSC-DCNL-WFJF-TLWL',
'CSTP-XDHW-NZKF-DVGF',
'GDSY-YSWF-HSWF-KJFH',
'PTZK-ZJXJ-WWBL-XSDM',
'ZHQW-HMFN-VPYX-RFZR',
'BCQM-FSFY-KRLM-ZGNG',
'TBJZ-ZZRX-VTSG-YTBM',
'HJPQ-TJHC-GLNH-TMVV',
'ZLQS-PDCT-CZRM-PNTF',
'YPMT-XDBH-PTCG-HSXS',
'QDYK-TWHN-HLXG-YLWD',
'SCDY-MRVN-CVWC-CBDK',
'BHWW-GPRF-JPPN-ZZZN',
'FFGB-YBBQ-WRBY-GJWR',
'QDDV-PFWG-SZYY-YNQF',
'ZLJP-ZTQC-XTVN-HTSW',
'KRTJ-DTSQ-DDRQ-HSJR',
'FJMT-ZVKF-LVKX-MQLB',
'FNRR-BZQT-QQYC-LNZM',
'XFBZ-LYXT-BLNG-PYPL',
'HJRT-NMBH-SNPH-YQCF',
'FFPY-GBHG-RNVH-JYDH',
'WHHF-KDGT-GSFG-RKSS',
'KGVC-BQWX-QWNP-LVNP',
'HXQJ-BNMH-TKZM-FXYP',
'TZFD-FVTM-SRLW-YFGD',
'LRFX-CXFS-CFNG-YGRB',
'QXLC-MDQS-KTFX-LBGG',
'PTCS-CPJY-SXRH-KDDB',
'DSFH-NDTB-SKRT-XVNN',
'YWRV-SJRH-TGLK-RGYZ',
'MXRK-TDMP-CLVR-GSVK',
'BVNG-QDLT-RNSD-ZNGN',
'SXWB-QGGN-YBMV-YRMT',
'QLMB-QGSX-HVPM-LRXB',
'NDXP-BGHW-KLBC-DRDW',
'CNHG-GBPL-MBBL-LYCR',
'MTYK-JMRY-WMHC-BJJK',
'HPYP-NHHV-QMMF-JCBS',
'CMJG-LLQG-KGWZ-VFMD',
'VMGT-HCCP-LTNS-HSMW',
'LZFB-BVFH-MZZP-NVMX',
'JDDD-SCDG-PNYP-ZJQP',
'VXMD-HWSQ-RHLR-KNNF',
'RTZW-QHYK-NKCS-QNNT',
'KPDF-WXKS-DKCB-QGDW',
'DBQG-VMNR-TKSV-RJPH',
'YVWK-MNKR-DQKB-VGQZ',
'PMGC-LCDB-VNDX-RWFS',
'PSTX-GWPS-CGXL-BGWM',
'JMRS-LRCQ-MZGS-HTSC',
'SXTL-PZXF-BTXD-QJXQ',
'VVLL-VYMT-LYXD-PFDB',
'DGMV-MVDM-XWVL-FVHX',
'FBFW-YKML-VGMM-BVWM',
'VCZZ-VJQG-MQDT-JRFR',
'JGLL-CRBB-XTTK-JXDH',
'QVCT-SCWP-LVBM-CQMW',
'NDWH-JDZS-RJNG-QQRN',
'DNCD-DKKB-HXZG-VGKY',
'JPGD-QNVJ-MBZW-LQZR',
'QLZJ-TZCF-RFPN-BBHQ',
'SCTC-BYCC-JRSQ-YTVM',
'QYHM-NGYQ-GSWK-RQLH',
'LNDC-CSFS-GCJR-FGJH',
'HYSV-NPLC-GCNL-KZTF',
'XRWC-GCFH-QNKT-XCNL',
'SHJC-GWVN-NQYT-KGNW',
'JSRL-QRBL-CRRD-KHWG',
'FBJJ-CXMR-GXGH-XBJZ',
'SSKS-MVJD-QTYJ-SPDX',
'SFYP-VTDT-BFWG-NNVX',
'MZKT-RLRR-HSGM-KCYK',
'MNQD-FYCB-WPPL-FQSL',
'YXLD-KTLT-JPXL-HRFH',
'FGKW-PNTJ-NJVK-HMCM',
'HBWB-ZLYH-ZHYP-TJRH',
'BTRW-RPFW-MSVB-CTXR',
'CGFL-PHSL-KYNW-RQPD',
'LKKY-HJTN-LGHL-VDVX',
'NQLL-YQWZ-CZNG-YXQD',
'JXVL-LRRT-MPMW-ZWVN',
'QNFW-HHYP-RXQQ-YXCS',
'JKNV-KBDJ-VLWX-SYDV',
'RKQP-MTWG-FYRK-GXBN',
'KDLM-BYJJ-QTFW-JVJJ',
'ZVYQ-HFRW-SQNS-MNMD',
'LMGW-VNDK-HMVD-WKNW',
'TXCJ-QRCV-QPXQ-PGZB',
'JPRN-JTDB-DRSV-SPJS',
'FFBH-SXDF-NFVJ-QDJG',
'XBRX-PPJD-DGJT-THPR',
'VHZK-XZGW-RLHG-KRBW',
'ZZPL-DPCD-SGMY-FRNY',
'CZQV-LPQQ-FMSL-MZHJ',
'FZDD-SPLN-XHKF-TSRX',
'NFPK-LJCT-KBYF-JQHM',
'QLLP-JQQD-DYQV-XHMX',
'SHMT-PDML-WVPS-TBFJ',
'YKVV-CHFB-TZNR-SXDP',
'KDMS-JFWQ-JRQJ-PBFZ',
'SHTV-HZGX-TRFC-ZCYW',
'CKNF-MHCV-VXSV-HKYD',
'BBBV-WQTK-HWKJ-GTVZ',
'MWKF-ZZJT-NDCM-XFSK',
'TXFB-SZJQ-GDYX-YRDR',
'ZCXY-FMGD-VCYR-WPDB',
'DZBR-SNJX-RXGT-JMTG',
'KHMH-QZVH-HKKP-HVRT',
'SKXZ-SRRQ-SGJX-GCLN',
'LVCH-MWTK-ZXWQ-TQMW',
'RVZJ-PPRQ-FPJZ-MLTL',
'FHNT-WRYN-CHWL-DBZB',
'ZFBD-LCYJ-FDGL-VZCP',
'LDGX-VWFF-RMBF-HJGQ',
'RDPW-BRSG-BMHZ-RJQG',
'JRHX-PZKQ-HTMF-GKZX',
'NRGJ-JWKY-NKJR-SVFK',
'VKCM-PNMP-BFKK-HQVB',
'QDMB-LNJP-WXHF-HCPF',
'LHYK-SLFW-RVWS-CVFR',
'XFMY-ZVTB-KPFM-QWZZ',
'ZNRP-TTXH-QHFB-NWSD',
'XRNR-WWCW-YLBX-QWVL',
'NZRV-PRRZ-ZQSH-CHJF',
'VQBG-QSCQ-TPFL-HLHP',
'KNKJ-PMHR-SSJS-JVJV',
'WQDX-ZHFS-KYZX-WBVC',
'YTMX-YYPL-HYCN-YLJL',
'QPLM-TQGY-MYVX-ZBYS',
'GQVN-LFQD-TGJM-YGML',
'BDSB-XBDH-KTKC-GVJX',
'KMLD-JZML-YGZL-YSKJ',
'DYDV-WDXC-YRRY-BHJC',
'BYQC-JWZJ-FQJH-LRKS',
'RHGT-CYFN-PMNH-PZZW',
'TGXV-XFWB-NHYQ-XHVT',
'XSVM-HVJB-BTZR-PKSP',
'BFVX-DDXF-QNLB-FTWK',
'KRPW-TFWY-QHKM-DBFX',
'XXQK-PMWM-PVFH-DYHG',
'WNQS-MPZM-HTDY-YRSQ',
'XHXB-XBGZ-KDBX-RCYW',
'KHZC-DQJC-BYYX-VHCR',
'LZCY-GBWB-SVDY-XGSK',
'LQJC-TLKJ-GBPW-TRSY',
'TVQJ-QNLZ-HSTW-YVCR',
'VDHC-CWRG-XQBC-SYYN',
'WRMV-XSPG-PLSN-XYVP',
'SHHF-FFXH-RGKY-MDPC',
'NSZP-MCHH-RNMD-DZZJ',
'XXNK-GXRN-XKQM-ZVVJ',
'TDTC-HRPL-QFJW-DZTX',
'QTWL-QBNR-PMFJ-XWSV',
'RCDH-YXQC-XDWW-VPHF',
'FSQK-WFMF-RVLC-FVXC',
'MCSL-TNDT-TSTL-GNQZ',
'SLLD-JGCV-KDGJ-PBVQ',
'JLDG-GKFD-SYHM-NQTH',
'HFVR-TCQC-NVNB-MPQS',
'PHWD-CJZR-PGFZ-HXTK',
'PWNM-XZHF-LXGV-LRVC',
'YMGW-BVKZ-GTQX-KMZX',
'CZGW-QKMC-DJBP-XMBT',
'BGFW-KGHC-MBHL-BCRC',
'TNLP-YGNT-NJLM-RXZJ',
'FLFK-XSLC-CLBZ-YGZH',
'RQZX-LPXF-FXSX-KLCQ',
'ZVZL-VRTT-MHBP-ZHMC',
'YPFS-JQFV-SZBP-SKHG',
'XSRM-CZYV-NPGG-DRCV',
'MHDD-WYGJ-MBLM-NVQD',
'JLCS-MQCN-MLRV-BSSD',
'DXML-LYQL-KHBC-KPSB',
'NJCG-LMHM-MDGM-GWYM',
'YGZT-FCBR-CXJF-WRVP',
'LJWT-HFHL-BLBM-ZZJJ',
'TBYW-NGJN-GMWY-PCJN',
'FBBH-NXSZ-QXSC-NHVX',
'XDDG-CSDF-JKMD-MQBV',
'NQGN-KDKJ-VGNX-JBHK',
'CJGS-BYXQ-LYSY-QWJQ',
'WMZK-DNZY-RMHL-FFBC',
'GXYN-RYLB-MQLK-QTWC',
'JJSW-FGVP-ZCZR-DMDK',
'DTXQ-ZRYS-WPQV-GMFW',
'HZLT-BFCL-KQNC-NVTF',
'SDFM-FTPY-GDPT-HMTF',
'NLVH-YVVZ-NVMR-KLLG',
'GWRX-DFYT-DNSQ-XKMR',
'MXFB-MPCJ-PLMV-TFYM',
'CNDB-XGXV-TWFQ-WGDR',
'VNKT-ZCYW-RPBQ-ZQFT',
'MBXJ-MLKY-BBCR-XBCX',
'BBWN-BBBT-JXVK-YJSB',
'JSST-NTVB-DDVZ-LMYR',
'QMVR-CSSH-CMQZ-LHLH',
'HLXS-BBMC-KXPD-GLNY',
'CFZL-TBXW-ZJZX-NTDQ',
'FXML-WXFX-NSZK-KCTM',
'NQNG-GHZR-QFNY-QWJX',
'TPFS-GWZJ-QTWF-DDHL',
'DJQF-FMHB-GKHZ-DGSC',
'LHVJ-RJNS-DVBD-YHSL',
'FVTN-RMRX-PCMC-MXGG',
'KZCF-ZRSR-ZFTT-QHYQ',
'DGLY-FPCB-KGKH-BVWK',
'YNBT-VWPB-PKHT-NRGR',
'MWVG-ZKMR-XBLL-JFLV',
'KKPY-PGSN-BDJQ-KVKX',
'HSWW-BPYT-FRXJ-FWPK',
'MPZX-NVNL-WTQP-GSQB',
'PYNJ-TYWZ-LMHX-QFWT',
'JCRX-DWNG-RVTW-WZGG',
'RDCG-XVDM-ZNDN-VDLH',
'GHPB-ZQQH-WHTK-BVXD',
'LRQY-ZBRY-SNRZ-SQCB',
'KJBX-LBCX-RXKJ-VJRW',
'FVXG-TKVR-KNNZ-CLKW',
'FMMD-YMSX-BLTG-BNJC',
'BSGP-NFGT-FCCJ-SCYS',
'PHWX-LPPR-FXCL-NZPL',
'XSZB-WDJZ-JQTP-LJGD',
'LXQQ-KVZD-HFPT-BZXY',
'BBLH-MKPH-WBGX-YLCQ',
'LXBX-YHVX-SRDZ-JMNM',
'ZKGN-QGZM-CLTP-WFXS',
'FQVB-SQDF-GQMD-VNCN',
'FLTC-PXQS-WFKV-ZWDQ',
'MHQV-RWKR-VRHW-THVN',
'LFHB-QTVF-MKPP-DGYV',
'JJMB-JDHG-GZQV-PSPY',
'NLWH-SWZX-DHQB-HZNB',
'RPMD-PGYY-CYFW-FHTF',
'FSNG-XTJF-MMPD-VYBG',
'CCKD-ZVCD-FYJL-GRPW',
'GDZP-MBCX-JLSR-YCMK',
'KMVL-JYTK-PKDJ-FNND',
'YVQJ-YKBJ-YHRX-NCRG',
'XBDJ-XRHR-PQGJ-SFBY',
'CNSF-WGSN-YNQK-GLXY',
'MYBZ-WWGJ-VRXZ-ZGDQ',
'GVLN-ZLPV-NBWM-GMLR',
'VDGB-YBJG-VMYW-NMCZ'
);