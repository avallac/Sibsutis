all:    clean server client
libNetwork.a: libNetwork.o
	ar rc libNetwork.a libNetwork.o
	ranlib libNetwork.a
libNetwork.o:   libNetwork.cpp
	gcc -c libNetwork.cpp
server: server.c libNetwork.a
	g++ -o server server.c -L. -lNetwork -fpermissive  -lpthread
client: client.c libNetwork.a
	g++ -o client client.c -L. -lNetwork
clean:
	rm -f *.o *.a server client
