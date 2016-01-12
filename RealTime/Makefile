all:	clean lab1
lab1:	lab1.cpp
	g++ -o lab1 lab1.cpp -I/usr/X11R6/include -L/usr/X11R6/lib -lX11 -Wno-c++11-compat-deprecated-writable-strings
clean:
	rm -f *.o *.a lab1 lab2
