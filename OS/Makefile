all:			clean main
main:			main.o libTerm.a libClock.a libCore.a libBuffer.a libObj.a libApps.a libString.a
				gcc -o main main.o -L. -lTerm -lClock -lCore -lBuffer -lObj -lApps -lstdc++ -lString
main.o:			main.cpp
				gcc -Wno-c++11-compat-deprecated-writable-strings -c main.cpp
libTerm.a:		libTerm.o
				ar rc libTerm.a libTerm.o
				ranlib libTerm.a
libTerm.o:		libTerm.cpp
				gcc -Wno-c++11-compat-deprecated-writable-strings -Wno-format-security -c libTerm.cpp
libClock.a:		libClock.o libTerm.a
				ar rc libClock.a libClock.o
				ranlib libClock.a
libClock.o:		libClock.cpp
				gcc -c libClock.cpp -lTerm
libCore.a:		libCore.o libTerm.a
				ar rc libCore.a libCore.o
				ranlib libCore.a
libCore.o:		libCore.cpp
				gcc -c libCore.cpp -lTerm
libBuffer.a:	libBuffer.o libTerm.a
				ar rc libBuffer.a libBuffer.o
				ranlib libBuffer.a
libBuffer.o:	libBuffer.cpp
				gcc -c libBuffer.cpp -lTerm
libObj.a:		libObj.o libTerm.a
				ar rc libObj.a libObj.o
				ranlib libObj.a
libObj.o:    	libObj.cpp
				gcc -Wno-c++11-compat-deprecated-writable-strings -c libObj.cpp -lTerm
libString.a:	libString.o libTerm.a
				ar rc libString.a libString.o
				ranlib libString.a
libString.o:   	libString.cpp
				gcc -Wno-c++11-compat-deprecated-writable-strings -c libString.cpp -lTerm
libApps.a:		libApps.o libTerm.a
				ar rc libApps.a libApps.o
				ranlib libApps.a
libApps.o:    	libApps.cpp
				gcc -Wno-c++11-compat-deprecated-writable-strings -c libApps.cpp -lTerm
clean:
				rm -f *.o *.a
