all:			clean main
main:			main.o libTerm.a libClock.a libCore.a libBuffer.a
				gcc -o main main.o -L. -lTerm -lClock -lCore -lBuffer -lstdc++
main.o:			main.cpp
				gcc -c main.cpp
libTerm.a:		libTerm.o
				ar rc libTerm.a libTerm.o
				ranlib libTerm.a
libTerm.o:		libTerm.cpp
				gcc -Wno-c++11-compat-deprecated-writable-strings -c libTerm.cpp
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
clean:
				rm -f *.o *.a
