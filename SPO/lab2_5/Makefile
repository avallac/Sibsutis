all:    clean server client_udp client_tcp
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
client_udp: client_udp.c libNetwork.a libTFTP.a
	g++ -o client_udp client_udp.c -L. -lNetwork -lTFTP 
client_tcp: client_tcp.c
	g++ -o client_tcp client_tcp.c
clean:
	rm -f *.o *.a server client_udp
