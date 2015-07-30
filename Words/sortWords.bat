sort words3.txt > words3.sort
sort words4.txt > words4.sort
sort words5.txt > words5.sort
sort words6.txt > words6.sort
sort wordsRest.txt > wordsRest.sort
sort /+3 words5.txt > words5ws.txt
copy /Y words*.sort words*.txt
echo Now run the program in eclipse, then run upload.bat
