# webscrapping
p1 = url("http://www.oxfordreference.com/view/10.1093/acref/9780199202720.001.0001/acref-9780199202720?hide=true&pageSize=100&sort=titlesort&source=%2F10.1093%2Facref%2F9780199202720.001.0001%2Facref-9780199202720")
htmlCode = readLines(p1)
close(p1)
htmlCode



abound = url("http://www.oxfordreference.com/view/10.1093/acref/9780199202720.001.0001/acref-9780199202720-e-2?rskey=cu9paO&amp;result=2")
htmlCode2 = readLines(abound)
close(abound)
htmlCode2

library(XML)
url <- "http://www.oxfordreference.com/view/10.1093/acref/9780199202720.001.0001/acref-9780199202720-e-13?rskey=cu9paO&result=2"
html <- htmlTreeParse(url, useInternalNodes=T)

xpathSApply(html, "//contentRoot", xmlValue)
htmlCode2[1448]





http://www.oxfordreference.com/

/view/10.1093/acref/9780199202720.001.0001/acref-9780199202720-e-2?rskey=cu9paO&amp;result=5