all:			clean checkLab2
checkLab2:		checkLab2.o libLab2.a
			gcc -o checkLab2 checkLab2.o -L. -lLab2
checkLab2.o:		checkLab2.c
			gcc -c checkLab2.c
libLab2.a:		libLab2.o
			ar rc libLab2.a libLab2.o
			ranlib libLab2.a
libLab2.o:		libLab2.c
			gcc -c libLab2.c
clean:
			rm -f *.o *.a
