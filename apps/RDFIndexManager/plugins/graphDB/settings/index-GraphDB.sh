#! /bin/sh

url=$1/repositories/$2/rdf-graphs/service?graph=$3

echo INDEX $4
date >> progress.out

shopt -s nullglob
for f in $4/*.owl $4/*.rdf $4/*.xml
do
  shopt -u nullglob
  echo $f
  echo $f >> progress.out
  curl ${url} -X POST --data-binary "@$f" -H "Content-Type: application/rdf+xml" >> curl.out
done

shopt -s nullglob
for f in $4/*.n3
do
  shopt -u nullglob
  fn=`basename "$f"`
  echo "split $f"
  echo $f >> progress.out
  date1=$(date +"%s")
  rm -rf cache
  mkdir cache
  split --line-bytes=10M -d $f cache/$fn.
  date2=$(date +"%s")
  diff=$(($date2-$date1))
  echo "split $(($diff / 60))m $(($diff % 60))s" >> progress.out

  echo "POST $fn"
  date1=$(date +"%s")
  for x in cache/*; do
    echo $x
    date11=$(date +"%s")
    curl -X POST -d "@$x" -H "Content-Type: text/rdf+n3" $url >> curl.out
    date21=$(date +"%s")
    diff=$(($date21-$date11))
    echo "POST $x $(($diff / 60))m $(($diff % 60))s" >> progress.out
  done
  date2=$(date +"%s")
  diff=$(($date2-$date1))
  echo "POST $fn $(($diff / 60))m $(($diff % 60))s" >> progress.out
done
 
shopt -s nullglob
for f in $4/*.nt
do
  shopt -u nullglob
  echo $f
  echo $f >> progress.out
  curl -X POST -d "@$f" -H "Content-Type: text/rdf+n3" $url >> curl.out
done

date >> progress.out

