# cleanup global environment
rm(list = ls(all = TRUE))

# Set working directory
setwd("~/Desktop/pdf/")


##########
# Hualde #
##########

# read data
hualde = read.csv("./hualde/hualdeSheet1.csv", header=TRUE)

str(hualde)
head(hualde)
names(hualde)



##########
# Oxford #
##########

source("./oxford/scrape.r")