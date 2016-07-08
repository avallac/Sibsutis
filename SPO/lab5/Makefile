all:    clean server client
libNetwork.a: libNetwork.o
	ar rc libNetwork.a libNetwork.o
	ranlib libNetwork.a
libNetwork.o:   libNetwork.cpp
	gcc -c libNetwork.cpp
libTFTP.a: libTFTP.o
	ar rc libTFTP.a libTFTP.o
	ranlib libTFTP.a
libTFTP.o:   libTFTP.cpp
	gcc -c libTFTP.cpp
server: server.c libNetwork.a libTFTP.a
	g++ -o server server.c -L. -lNetwork -lTFTP -fpermissive  -lpthread
client: client.c libNetwork.a libTFTP.a
	g++ -o client client.c -L. -lNetwork -lTFTP 
clean:
	rm -f *.o *.a server client
