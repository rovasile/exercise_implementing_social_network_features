echo "Update name: "
read varname
git commit -am "$varname"
git push origin master
