all:			clean checkLab2 checkLab3 checkLab4
checkLab2:		checkLab2.o libLab2.a
			gcc -o checkLab2 checkLab2.o -L. -lLab2
checkLab2.o:		checkLab2.c
			gcc -c checkLab2.c
libLab2.a:		libLab2.o
			ar rc libLab2.a libLab2.o
			ranlib libLab2.a
libLab2.o:		libLab2.c
			gcc -c libLab2.c
checkLab3:		checkLab3.o libLab3.a
			gcc -o checkLab3 checkLab3.o -L. -lLab3
checkLab3.o:		checkLab3.c
			gcc -c checkLab3.c
libLab3.a:		libLab3.o
			ar rc libLab3.a libLab3.o
			ranlib libLab3.a
libLab3.o:		libLab3.c
			gcc -c libLab3.c
checkLab4:		checkLab4.o libLab4.a
			gcc -o checkLab4 checkLab4.o -L. -lLab4 -lLab3
checkLab4.o:		checkLab4.c
			gcc -c checkLab4.c
libLab4.a:		libLab4.o
			ar rc libLab4.a libLab4.o
			ranlib libLab4.a
libLab4.o:		libLab4.c
			gcc -c libLab4.c
clean:
			rm -f *.o *.a
