all:			clean main
main:			main.o libTerm.a
				gcc -o main main.o -L. -lTerm
main.o:			main.c
				gcc -c main.c
libTerm.a:		libTerm.o
				ar rc libTerm.a libTerm.o
				ranlib libTerm.a
libTerm.o:		libTerm.c
				gcc -c libTerm.c
clean:
				rm -f *.o *.a
