all:	clean server client
server:	server.cpp
	g++ -o server server.cpp -lboost_system
client:	client.cpp
	g++ -o client client.cpp -lboost_system -lpthread
clean:
	rm -f *.o *.a server client
